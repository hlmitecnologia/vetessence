<?php

namespace App\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Http;

class WebmaniaProvider implements NfeProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('nfe.webmania.base_url', 'https://webmania.com.br/api/1');
    }

    public function emitir(NfeConfig $config, Invoice $invoice): NfeResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/nfe/emissao/", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['error'] ?? $body['erro'] ?? 'Erro ao emitir NF-e via Webmania',
                $body
            );
        }

        return NfeResult::success(
            nfeNumber: $body['numero'] ?? $body['nfe'] ?? '',
            nfeKey: $body['chave'] ?? $body['chave_nfe'] ?? '',
            xmlUrl: $body['xml'] ?? '',
            pdfUrl: $body['pdf'] ?? '',
            danfeUrl: $body['danfe'] ?? '',
            rawResponse: $body,
        );
    }

    public function emitirTransferencia(NfeConfig $config, array $data): NfeResult
    {
        $payload = $this->buildTransferPayload($config, $data);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/nfe/emissao/", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['error'] ?? $body['erro'] ?? 'Erro ao emitir NF-e de transferência via Webmania',
                $body
            );
        }

        return NfeResult::success(
            nfeNumber: $body['numero'] ?? $body['nfe'] ?? '',
            nfeKey: $body['chave'] ?? $body['chave_nfe'] ?? '',
            xmlUrl: $body['xml'] ?? '',
            pdfUrl: $body['pdf'] ?? '',
            danfeUrl: $body['danfe'] ?? '',
            rawResponse: $body,
        );
    }

    public function consultar(NfeConfig $config, string $nfeNumber): NfeResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->get("{$this->baseUrl}/nfe/{$nfeNumber}/");

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['error'] ?? $body['erro'] ?? 'Erro ao consultar NF-e via Webmania',
                $body
            );
        }

        return NfeResult::success(
            nfeNumber: $body['numero'] ?? '',
            nfeKey: $body['chave'] ?? '',
            xmlUrl: $body['xml'] ?? '',
            pdfUrl: $body['pdf'] ?? '',
            danfeUrl: $body['danfe'] ?? '',
            rawResponse: $body,
        );
    }

    public function cancelar(NfeConfig $config, string $nfeNumber, string $motivo, ?string $nfeKey = null): NfeResult
    {
        $chave = $nfeKey ?: $nfeNumber;

        $response = Http::withHeaders($this->headers($config))
            ->put("{$this->baseUrl}/nfe/cancelar/", [
                'chave' => $chave,
                'motivo' => $motivo,
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['error'] ?? 'Erro ao cancelar NF-e via Webmania',
                $body
            );
        }

        return NfeResult::success(
            nfeNumber: $nfeNumber,
            rawResponse: $body,
        );
    }

    protected function headers(NfeConfig $config): array
    {
        return [
            'X-Consumer-Key' => $config->webmania_consumer_key,
            'X-Consumer-Secret' => $config->webmania_consumer_secret,
            'X-Access-Token' => $config->webmania_access_token,
            'X-Access-Token-Secret' => $config->webmania_access_token_secret,
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(NfeConfig $config, Invoice $invoice): array
    {
        $tutor = $invoice->tutor;
        $branch = $invoice->branch;
        $items = $invoice->items()->where('item_type', 'product')->get();

        $produtos = $items->map(function (InvoiceItem $item) {
            $product = $item->product;

            return [
                'nome' => $item->description,
                'ncm' => $product?->ncm ?? '99999999',
                'cest' => $product?->cest,
                'cfop' => $product?->cfop ?? '5102',
                'quantidade' => (float) $item->quantity,
                'unidade' => $product?->unit ?? 'UN',
                'valor_unitario' => (float) $item->unit_price,
                'valor_total' => (float) $item->total,
                'cst' => $product?->cst ?? '00',
                'csosn' => $product?->csosn,
            ];
        })->toArray();

        return [
            'cnpj' => $branch->cnpj,
            'ie' => $branch->ie,
            'crt' => $branch->crt,
            'municipio_ibge' => $branch->municipio_ibge,
            'serie' => $branch->serie ?? '1',
            'ambiente' => $config->ambiente === 'producao' ? 1 : 2,
            'natureza_operacao' => 'Venda de mercadoria',
            'cliente' => [
                'cpf_cnpj' => preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? ''),
                'nome' => $tutor->name,
                'email' => $tutor->email ?? '',
                'telefone' => preg_replace('/\D/', '', $tutor->phone ?? ''),
                'endereco' => $tutor->address ?? '',
                'numero' => $tutor->number ?? 'S/N',
                'bairro' => $tutor->neighborhood ?? '',
                'cidade' => $tutor->city ?? '',
                'uf' => $tutor->state ?? '',
                'cep' => preg_replace('/\D/', '', $tutor->zipcode ?? ''),
            ],
            'produtos' => $produtos,
        ];
    }

    protected function buildTransferPayload(NfeConfig $config, array $data): array
    {
        $fromBranch = $data['from_branch'];
        $toBranch = $data['to_branch'];
        $product = $data['product'];

        return [
            'cnpj' => $fromBranch->cnpj,
            'ie' => $fromBranch->ie,
            'crt' => $fromBranch->crt,
            'municipio_ibge' => $fromBranch->municipio_ibge,
            'serie' => $fromBranch->serie ?? '1',
            'ambiente' => $config->ambiente === 'producao' ? 1 : 2,
            'natureza_operacao' => 'Transferência entre filiais',
            'finalidade' => '1',
            'cliente' => [
                'cpf_cnpj' => preg_replace('/\D/', '', $toBranch->cnpj ?? ''),
                'nome' => $toBranch->name,
                'ie' => $toBranch->ie,
                'endereco' => $toBranch->address ?? '',
                'numero' => $toBranch->number ?? 'S/N',
                'bairro' => $toBranch->neighborhood ?? '',
                'cidade' => $toBranch->city ?? '',
                'uf' => $toBranch->state ?? '',
                'cep' => preg_replace('/\D/', '', $toBranch->zipcode ?? ''),
            ],
            'produtos' => [
                [
                    'nome' => $product->name,
                    'ncm' => $product->ncm ?? '99999999',
                    'cest' => $product->cest,
                    'cfop' => $product->cfop ?? '5949',
                    'quantidade' => (float) $data['quantity'],
                    'unidade' => $product->unit ?? 'UN',
                    'valor_unitario' => (float) $product->cost_price,
                    'valor_total' => (float) ($data['quantity'] * $product->cost_price),
                    'cst' => $product->cst ?? '00',
                    'csosn' => $product->csosn,
                ],
            ],
        ];
    }
}
