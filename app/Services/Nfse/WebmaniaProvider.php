<?php

namespace App\Services\Nfse;

use App\Models\NfseConfig;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

class WebmaniaProvider implements NfseProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('nfse.webmania.base_url', 'https://api.webmania.com.br');
    }

    public function emitir(NfseConfig $config, Invoice $invoice): NfseResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/2/nfse/emissao/", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfseResult::error(
                $body['error'] ?? $body['erro'] ?? 'Erro ao emitir NFSe via Webmania',
                $body
            );
        }

        return NfseResult::success(
            nfseNumber: $body['nfse'] ?? $body['numero'] ?? '',
            nfseCode: $body['codigo'] ?? '',
            xmlUrl: $body['xml'] ?? '',
            pdfUrl: $body['pdf'] ?? '',
            rpsNumber: $body['rps'] ?? '',
            verificationCode: $body['codigo_verificacao'] ?? '',
            rawResponse: $body,
        );
    }

    public function consultar(NfseConfig $config, string $nfseNumber): NfseResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->get("{$this->baseUrl}/2/nfse/{$nfseNumber}/");

        $body = $response->json();

        if (!$response->successful()) {
            return NfseResult::error(
                $body['error'] ?? 'Erro ao consultar NFSe via Webmania',
                $body
            );
        }

        return NfseResult::success(
            nfseNumber: $body['nfse'] ?? '',
            nfseCode: $body['codigo'] ?? '',
            xmlUrl: $body['xml'] ?? '',
            pdfUrl: $body['pdf'] ?? '',
            rpsNumber: $body['rps'] ?? '',
            verificationCode: $body['codigo_verificacao'] ?? '',
            rawResponse: $body,
        );
    }

    public function cancelar(NfseConfig $config, string $nfseNumber, string $motivo, ?string $uuid = null): NfseResult
    {
        $payload = [
            'motivo' => (int) $motivo,
        ];

        if ($uuid) {
            $payload['uuid'] = $uuid;
        }

        $response = Http::withHeaders($this->headers($config))
            ->put("{$this->baseUrl}/2/nfse/cancelar", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfseResult::error(
                $body['error'] ?? 'Erro ao cancelar NFSe via Webmania',
                $body
            );
        }

        return NfseResult::success(
            nfseNumber: $nfseNumber,
            rawResponse: $body,
        );
    }

    protected function headers(NfseConfig $config): array
    {
        return [
            'Authorization' => 'Bearer ' . $config->webmania_access_token,
            'Content-Type' => 'application/json',
        ];
    }

    protected function formatPhone(?string $phone): string
    {
        $clean = preg_replace('/\D/', '', $phone ?? '');
        return strlen($clean) >= 8 ? $clean : '0000000000';
    }

    protected function buildPayload(NfseConfig $config, Invoice $invoice): array
    {
        $tutor = $invoice->tutor;
        $branch = $invoice->branch;

        return [
            'ambiente' => $config->ambiente === 'producao' ? 1 : 2,
            'rps' => [
                [
                    'cnpj' => $branch->cnpj,
                    'municipio_ibge' => $branch->municipio_ibge,
                    'regime_tributario' => $branch->regime_tributario,
                    'serie' => $branch->serie ?? '1',
                    'rps_tipo' => '1',
                    'tomador' => [
                        'cpf_cnpj' => preg_replace('/\D/', '', $tutor->cpf ?? $tutor->cnpj ?? ''),
                        'nome' => $tutor->name,
                        'email' => $tutor->email ?? '',
                        'telefone' => $this->formatPhone($tutor->phone ?? $branch->phone ?? ''),
                        'logradouro' => $tutor->address ?? '',
                        'numero' => $tutor->number ?? 'S/N',
                        'bairro' => $tutor->neighborhood ?? '',
                        'cidade' => $tutor->city ?? '',
                        'uf' => $tutor->state ?? '',
                        'cep' => preg_replace('/\D/', '', $tutor->zipcode ?? ''),
                    ],
                    'servico' => [
                        'discriminacao' => $invoice->description ?? "Serviços veterinários - Fatura #{$invoice->id}",
                        'valor_servicos' => (float) $invoice->total,
                        'iss_retido' => 2,
                    ],
                ],
            ],
        ];
    }
}
