<?php

namespace App\Services\Nfse;

use App\Models\NfseConfig;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

class NfeIoProvider implements NfseProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('nfse.nfeio.base_url', 'https://api.nfe.io');
    }

    public function emitir(NfseConfig $config, Invoice $invoice): NfseResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/v1/companies/{$config->nfeio_company_id}/serviceinvoices", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['erro'] ?? $body['error'] ?? 'Erro ao emitir NFSe via NFE.io';
            return NfseResult::error($error, $body);
        }

        return NfseResult::success(
            nfseNumber: (string) ($body['id'] ?? ''),
            nfseCode: (string) ($body['number'] ?? ''),
            xmlUrl: '',
            pdfUrl: '',
            rpsNumber: (string) ($body['rpsNumber'] ?? ''),
            verificationCode: $body['checkCode'] ?? '',
            rawResponse: $body,
        );
    }

    public function consultar(NfseConfig $config, string $nfseNumber): NfseResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->get("{$this->baseUrl}/v1/companies/{$config->nfeio_company_id}/serviceinvoices/{$nfseNumber}");

        $body = $response->json();

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['erro'] ?? $body['error'] ?? 'Erro ao consultar NFSe via NFE.io';
            return NfseResult::error($error, $body);
        }

        return NfseResult::success(
            nfseNumber: (string) ($body['id'] ?? ''),
            nfseCode: (string) ($body['number'] ?? ''),
            xmlUrl: '',
            pdfUrl: '',
            rpsNumber: (string) ($body['rpsNumber'] ?? ''),
            verificationCode: $body['checkCode'] ?? '',
            rawResponse: $body,
        );
    }

    public function cancelar(NfseConfig $config, string $nfseNumber, string $motivo, ?string $uuid = null): NfseResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->delete("{$this->baseUrl}/v1/companies/{$config->nfeio_company_id}/serviceinvoices/{$nfseNumber}");

        $body = $response->json();

        if (!$response->successful()) {
            $error = $body['errors'][0]['message'] ?? $body['erro'] ?? $body['error'] ?? 'Erro ao cancelar NFSe via NFE.io';
            return NfseResult::error($error, $body);
        }

        return NfseResult::success(
            nfseNumber: (string) ($body['id'] ?? $nfseNumber),
            nfseCode: (string) ($body['number'] ?? ''),
            verificationCode: $body['checkCode'] ?? '',
            rawResponse: $body,
        );
    }

    protected function headers(NfseConfig $config): array
    {
        return [
            'Authorization' => 'Basic ' . $config->nfeio_api_key,
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(NfseConfig $config, Invoice $invoice): array
    {
        $tutor = $invoice->tutor;
        $branch = $invoice->branch;

        $borrowerCpfCnpj = preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? '');
        $valor = (float) $invoice->total;
        $descricao = $invoice->description ?? "Serviços veterinários - Fatura #{$invoice->id}";

        $payload = [
            'cityServiceCode' => $branch->city_service_code ?? $branch->municipio_ibge ?? '',
            'description' => $descricao,
            'servicesAmount' => $valor,
        ];

        if (!empty($borrowerCpfCnpj)) {
            $payload['borrower'] = [
                'federalTaxNumber' => (int) $borrowerCpfCnpj,
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
            ];
        }

        return $payload;
    }
}
