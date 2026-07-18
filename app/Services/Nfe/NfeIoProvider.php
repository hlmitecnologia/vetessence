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
        $raw = is_array($body) ? $body : (is_string($body) ? ['message' => $body] : null);
        $body = is_array($body) ? $body : [];

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? ($raw['message'] ?? 'Erro ao emitir NF-e via NFE.io');
            return NfeResult::error($error, $raw);
        }

        $invoiceData = $body['productInvoice'] ?? $body;

        return NfeResult::success(
            nfeNumber: (string) ($invoiceData['number'] ?? $body['number'] ?? ''),
            nfeKey: $invoiceData['accessKey'] ?? $invoiceData['chave'] ?? '',
            xmlUrl: '',
            pdfUrl: '',
            danfeUrl: '',
            rawResponse: $raw,
        );
    }

    public function emitirNfce(NfeConfig $config, Invoice $invoice): NfeResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/v2/companies/{$config->nfeio_company_id}/consumerinvoices", $payload);

        $body = $response->json();
        $raw = is_array($body) ? $body : (is_string($body) ? ['message' => $body] : null);
        $body = is_array($body) ? $body : [];

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? ($raw['message'] ?? 'Erro ao emitir NFC-e via NFE.io');
            return NfeResult::error($error, $raw);
        }

        $invoiceData = $body['consumerInvoice'] ?? $body;

        return NfeResult::success(
            nfeNumber: (string) ($invoiceData['number'] ?? $body['number'] ?? ''),
            nfeKey: $invoiceData['accessKey'] ?? $invoiceData['chave'] ?? '',
            xmlUrl: '',
            pdfUrl: '',
            danfeUrl: '',
            rawResponse: $raw,
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
        $raw = is_array($body) ? $body : (is_string($body) ? ['message' => $body] : null);
        $body = is_array($body) ? $body : [];

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? ($raw['message'] ?? 'Erro ao consultar NF-e via NFE.io');
            return NfeResult::error($error, $raw);
        }

        $invoiceData = $body['productInvoice'] ?? $body;

        return NfeResult::success(
            nfeNumber: (string) ($invoiceData['number'] ?? ''),
            nfeKey: $invoiceData['accessKey'] ?? '',
            xmlUrl: '',
            pdfUrl: '',
            danfeUrl: '',
            rawResponse: $raw,
        );
    }

    public function cancelar(NfeConfig $config, string $nfeNumber, string $motivo, ?string $nfeKey = null): NfeResult
    {
        $query = $motivo ? ['reason' => $motivo] : [];

        $response = Http::withHeaders($this->headers($config))
            ->delete("{$this->baseUrl}/v2/companies/{$config->nfeio_company_id}/productinvoices/{$nfeNumber}", $query);

        $body = $response->json();
        $raw = is_array($body) ? $body : (is_string($body) ? ['message' => $body] : null);
        $body = is_array($body) ? $body : [];

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['message'] ?? $body['error'] ?? ($raw['message'] ?? 'Erro ao cancelar NF-e via NFE.io');
            return NfeResult::error($error, $raw);
        }

        return NfeResult::success(
            nfeNumber: $nfeNumber,
            rawResponse: $raw,
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

        $phoneDigits = preg_replace('/\D/', '', $tutor->phone ?? '');
        if (strlen($phoneDigits) < 8) {
            $phoneDigits = preg_replace('/\D/', '', $branch->phone ?? '');
        }
        $phoneNumber = strlen($phoneDigits) >= 8 ? $phoneDigits : str_pad($phoneDigits, 8, '0');

        $produtos = $items->map(function (InvoiceItem $item) {
            $product = $item->product;
            $total = (float) $item->total;
            $icmsAliquot = 18.0;
            $icmsValue = round($total * $icmsAliquot / 100, 2);
            $ncm = $product?->ncm ?? '99999999';
            $ncm = preg_replace('/\D/', '', $ncm);

            return [
                'code' => (string) ($product?->id ?? $item->id),
                'description' => $item->description,
                'ncm' => strlen($ncm) >= 2 ? $ncm : '99999999',
                'cfop' => $product?->cfop ?? '5102',
                'quantity' => (float) $item->quantity,
                'unitAmount' => (float) $item->unit_price,
                'totalAmount' => $total,
                'tax' => [
                    'icms' => [
                        'csosn' => '102',
                        'origin' => 0,
                        'baseCalculation' => $total,
                        'aliquot' => $icmsAliquot,
                        'value' => $icmsValue,
                    ],
                ],
            ];
        })->toArray();

        $buyerCpfCnpj = preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? '');

        return [
            'environment' => $config->environment ?? 'Production',
            'orderNumber' => (string) $invoice->id,
            'description' => $invoice->description ?? "Venda de produtos - Fatura #{$invoice->id}",
            'buyer' => [
                'federalTaxNumber' => (int) $buyerCpfCnpj,
                'name' => $tutor->name,
                'email' => $tutor->email ?? '',
                'phoneNumber' => $phoneNumber,
                'address' => [
                    'country' => 'BRA',
                    'street' => $tutor->address ?? '',
                    'number' => $tutor->number ?? 'S/N',
                    'additionalInformation' => $tutor->complement ?? '',
                    'district' => $tutor->neighborhood ?? '',
                    'city' => [
                        'code' => $tutor->city_ibge ?? $branch->municipio_ibge ?? '',
                        'name' => $tutor->city ?? $branch->city ?? '',
                    ],
                    'state' => strtoupper(substr($tutor->state ?? $branch->state ?? 'SP', 0, 2)),
                    'postalCode' => preg_replace('/\D/', '', $tutor->zipcode ?? ''),
                ],
            ],
            'items' => $produtos,
        ];
    }
}
