<?php

namespace App\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\Http;

class FocusNfeProvider implements NfeProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('nfe.focusnfe.base_url', 'https://api.focusnfe.com.br');
    }

    public function emitir(NfeConfig $config, Invoice $invoice): NfeResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withBasicAuth($config->focusnfe_token, '')
            ->post("{$this->baseUrl}/v2/nfe?ref={$invoice->id}", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['erro'] ?? $body['error'] ?? 'Erro ao emitir NF-e via FocusNFe',
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
        return NfeResult::error('FocusNFe não suporta emissão de NF-e de transferência.');
    }

    public function consultar(NfeConfig $config, string $nfeNumber): NfeResult
    {
        $response = Http::withBasicAuth($config->focusnfe_token, '')
            ->get("{$this->baseUrl}/v2/nfe/{$nfeNumber}");

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['erro'] ?? $body['error'] ?? 'Erro ao consultar NF-e via FocusNFe',
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
        $response = Http::withBasicAuth($config->focusnfe_token, '')
            ->delete("{$this->baseUrl}/v2/nfe/{$nfeNumber}?motivo=" . urlencode($motivo));

        $body = $response->json();

        if (!$response->successful()) {
            return NfeResult::error(
                $body['erro'] ?? $body['error'] ?? 'Erro ao cancelar NF-e via FocusNFe',
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
                'cest' => $product?->cest,
                'cfop' => $product?->cfop ?? '5102',
                'quantidade' => (float) $item->quantity,
                'unidade' => $product?->unit ?? 'UN',
                'valor_unitario' => (float) $item->unit_price,
                'valor_total' => (float) $item->total,
                'cst' => $product?->cst ?? '00',
                'csosn' => $product?->csosn,
                'icms_origem' => $product?->icms_origin ?? 0,
                'icms_cst' => $product?->icms_cst,
                'icms_modbc' => $product?->icms_modbc,
                'icms_vbc' => (float) ($product?->icms_vbc ?? 0),
                'icms_picms' => (float) ($product?->icms_picms ?? 0),
                'icms_predbc' => (float) ($product?->icms_predbc ?? 0),
                'ipi_cst' => $product?->ipi_cst,
                'ipi_aliquota' => (float) ($product?->ipi_aliquot ?? 0),
                'pis_cst' => $product?->pis_cst,
                'cofins_cst' => $product?->cofins_cst,
                'peso_kg' => (float) ($product?->weight_kg ?? 0),
            ];
        })->toArray();

        return [
            'cnpj' => $branch->cnpj,
            'ie' => $branch->ie,
            'crt' => $branch->crt,
            'municipio_ibge' => $branch->municipio_ibge,
            'serie' => $branch->serie ?? '1',
            'ambiente' => $config->ambiente,
            'nat_op' => 'Venda de mercadoria',
            'indicador_presenca' => '1',
            'operacao' => '1',
            'destinatario' => [
                'cpf_cnpj' => preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? ''),
                'nome' => $tutor->name,
                'email' => $tutor->email ?? '',
                'telefone' => preg_replace('/\D/', '', $tutor->phone ?? ''),
                'logradouro' => $tutor->address ?? '',
                'numero' => $tutor->number ?? 'S/N',
                'bairro' => $tutor->neighborhood ?? '',
                'cidade' => $tutor->city ?? '',
                'uf' => $tutor->state ?? '',
                'cep' => preg_replace('/\D/', '', $tutor->zipcode ?? ''),
            ],
            'produtos' => $produtos,
        ];
    }
}
