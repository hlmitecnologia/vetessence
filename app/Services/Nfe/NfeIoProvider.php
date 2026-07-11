<?php

namespace App\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Http;

class NfeIoProvider implements NfeProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('nfe.nfeio.base_url', 'https://api.nfe.io');
    }

    public function emitir(NfeConfig $config, Invoice $invoice): NfeResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders(['X-Api-Key' => $config->nfeio_api_key])
            ->post("{$this->baseUrl}/v1/nfe", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['message'] ?? $body['error'] ?? 'Erro ao emitir NF-e via NFE.io',
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
        return NfeResult::error('NFE.io não suporta emissão de NF-e de transferência.');
    }

    public function consultar(NfeConfig $config, string $nfeNumber): NfeResult
    {
        $response = Http::withHeaders(['X-Api-Key' => $config->nfeio_api_key])
            ->get("{$this->baseUrl}/v1/nfe/{$nfeNumber}");

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['message'] ?? $body['error'] ?? 'Erro ao consultar NF-e via NFE.io',
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

    public function cancelar(NfeConfig $config, string $nfeNumber, string $motivo): NfeResult
    {
        $response = Http::withHeaders(['X-Api-Key' => $config->nfeio_api_key])
            ->post("{$this->baseUrl}/v1/nfe/{$nfeNumber}/cancelar", [
                'motivo' => $motivo,
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['message'] ?? $body['error'] ?? 'Erro ao cancelar NF-e via NFE.io',
                $body
            );
        }

        return NfeResult::success(
            nfeNumber: $nfeNumber,
            rawResponse: $body,
        );
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
                'cfop' => $product?->cfop ?? '5102',
                'quantidade' => (float) $item->quantity,
                'unidade' => $product?->unit ?? 'UN',
                'valor_unitario' => (float) $item->unit_price,
                'valor_total' => (float) $item->total,
                'cst' => $product?->cst ?? '00',
                'icms_origem' => $product?->icms_origin ?? 0,
                'peso_kg' => (float) ($product?->weight_kg ?? 0),
            ];
        })->toArray();

        return [
            'cnpj_emitente' => $branch->cnpj,
            'ie_emitente' => $branch->ie,
            'crt_emitente' => $branch->crt,
            'codigo_ibge_municipio' => $branch->municipio_ibge,
            'serie' => $branch->serie ?? '1',
            'ambiente' => $config->ambiente,
            'natureza_operacao' => 'Venda de mercadoria',
            'cpf_cnpj_destinatario' => preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? ''),
            'nome_destinatario' => $tutor->name,
            'email_destinatario' => $tutor->email ?? '',
            'telefone_destinatario' => preg_replace('/\D/', '', $tutor->phone ?? ''),
            'logradouro_destinatario' => $tutor->address ?? '',
            'numero_destinatario' => $tutor->number ?? 'S/N',
            'bairro_destinatario' => $tutor->neighborhood ?? '',
            'cidade_destinatario' => $tutor->city ?? '',
            'uf_destinatario' => $tutor->state ?? '',
            'cep_destinatario' => preg_replace('/\D/', '', $tutor->zipcode ?? ''),
            'itens' => $produtos,
        ];
    }
}
