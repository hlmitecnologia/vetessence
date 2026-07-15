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
        $this->baseUrl = config('nfe.nfeio.base_url', 'https://api.nfse.io');
    }

    public function emitir(NfeConfig $config, Invoice $invoice): NfeResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/v2/companies/{$config->nfeio_company_id}/productinvoices", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? 'Erro ao emitir NF-e via NFE.io';
            return NfeResult::error($error, $body);
        }

        $invoiceData = $body['productInvoice'] ?? $body;

        return NfeResult::success(
            nfeNumber: (string) ($invoiceData['number'] ?? $body['number'] ?? ''),
            nfeKey: $invoiceData['accessKey'] ?? $invoiceData['chave'] ?? '',
            xmlUrl: '',
            pdfUrl: '',
            danfeUrl: '',
            rawResponse: $body,
        );
    }

    public function emitirTransferencia(NfeConfig $config, array $data): NfeResult
    {
        return NfeResult::error('NFE.io não suporta emissão de NF-e de transferência.');
    }

    public function consultar(NfeConfig $config, string $nfeNumber): NfeResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->get("{$this->baseUrl}/v2/companies/{$config->nfeio_company_id}/productinvoices/{$nfeNumber}");

        $body = $response->json();

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? 'Erro ao consultar NF-e via NFE.io';
            return NfeResult::error($error, $body);
        }

        $invoiceData = $body['productInvoice'] ?? $body;

        return NfeResult::success(
            nfeNumber: (string) ($invoiceData['number'] ?? ''),
            nfeKey: $invoiceData['accessKey'] ?? '',
            xmlUrl: '',
            pdfUrl: '',
            danfeUrl: '',
            rawResponse: $body,
        );
    }

    public function cancelar(NfeConfig $config, string $nfeNumber, string $motivo, ?string $nfeKey = null): NfeResult
    {
        $query = $motivo ? ['reason' => $motivo] : [];

        $response = Http::withHeaders($this->headers($config))
            ->delete("{$this->baseUrl}/v2/companies/{$config->nfeio_company_id}/productinvoices/{$nfeNumber}", $query);

        $body = $response->json();

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? 'Erro ao cancelar NF-e via NFE.io';
            return NfeResult::error($error, $body);
        }

        return NfeResult::success(
            nfeNumber: $nfeNumber,
            rawResponse: $body,
        );
    }

    protected function headers(NfeConfig $config): array
    {
        return [
            'Authorization' => 'Basic ' . $config->nfeio_api_key,
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
                'code' => (string) ($product?->id ?? $item->id),
                'description' => $item->description,
                'ncm' => $product?->ncm ?? '99999999',
                'cfop' => $product?->cfop ?? '5102',
                'quantity' => (float) $item->quantity,
                'unitAmount' => (float) $item->unit_price,
                'totalAmount' => (float) $item->total,
            ];
        })->toArray();

        $buyerCpfCnpj = preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? '');

        return [
            'operationNature' => 'Venda de mercadoria',
            'operationType' => 'Outgoing',
            'buyer' => [
                'federalTaxNumber' => (int) $buyerCpfCnpj,
                'name' => $tutor->name,
                'email' => $tutor->email ?? '',
                'address' => [
                    'country' => 'BRA',
                    'street' => $tutor->address ?? '',
                    'number' => $tutor->number ?? 'S/N',
                    'additionalInformation' => $tutor->complement ?? '',
                    'district' => $tutor->neighborhood ?? '',
                    'city' => [
                        'code' => $tutor->city_ibge ?? '',
                        'name' => $tutor->city ?? '',
                    ],
                    'state' => $tutor->state ?? '',
                    'postalCode' => preg_replace('/\D/', '', $tutor->zipcode ?? ''),
                ],
            ],
            'items' => $produtos,
        ];
    }
}
