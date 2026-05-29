<?php

namespace App\Services\Nfse;

use App\Models\NfseConfig;
use App\Models\Invoice;
use Illuminate\Support\Facades\Http;

class SpedyProvider implements NfseProvider
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('nfse.spedy.base_url', 'https://api.spedy.com.br');
    }

    public function emitir(NfseConfig $config, Invoice $invoice): NfseResult
    {
        $payload = $this->buildPayload($config, $invoice);

        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/v1/nfse", $payload);

        $body = $response->json();

        if (!$response->successful()) {
            return NfseResult::error(
                $body['erro'] ?? $body['error'] ?? 'Erro ao emitir NFSe via Spedy',
                $body
            );
        }

        return NfseResult::success(
            nfseNumber: $body['nfse'] ?? $body['numero'] ?? '',
            nfseCode: $body['codigo'] ?? $body['codigo_verificacao'] ?? '',
            xmlUrl: $body['xml'] ?? '',
            pdfUrl: $body['pdf'] ?? '',
            rpsNumber: $body['rps'] ?? $body['numero_rps'] ?? '',
            verificationCode: $body['codigo_verificacao'] ?? '',
            rawResponse: $body,
        );
    }

    public function consultar(NfseConfig $config, string $nfseNumber): NfseResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->get("{$this->baseUrl}/v1/nfse/{$nfseNumber}");

        $body = $response->json();

        if (!$response->successful()) {
            return NfseResult::error(
                $body['erro'] ?? $body['error'] ?? 'Erro ao consultar NFSe via Spedy',
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

    public function cancelar(NfseConfig $config, string $nfseNumber, string $motivo): NfseResult
    {
        $response = Http::withHeaders($this->headers($config))
            ->post("{$this->baseUrl}/v1/nfse/{$nfseNumber}/cancelar", [
                'motivo' => $motivo,
            ]);

        $body = $response->json();

        if (!$response->successful()) {
            return NfseResult::error(
                $body['erro'] ?? $body['error'] ?? 'Erro ao cancelar NFSe via Spedy',
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
            'X-Api-Key' => $config->spedy_api_key,
            'X-Api-Secret' => $config->spedy_api_secret,
            'Content-Type' => 'application/json',
        ];
    }

    protected function buildPayload(NfseConfig $config, Invoice $invoice): array
    {
        $tutor = $invoice->tutor;
        $branch = $invoice->branch;

        return [
            'cnpj' => $branch->cnpj,
            'municipio_ibge' => $branch->municipio_ibge,
            'regime_tributario' => $branch->regime_tributario,
            'serie' => $branch->serie,
            'ambiente' => $config->ambiente,
            'rps_tipo' => '1',
            'tomador' => [
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
            'servico' => [
                'descricao' => $invoice->description ?? "Serviços veterinários - Fatura #{$invoice->id}",
                'valor' => (float) $invoice->total,
                'iss_retido' => false,
            ],
        ];
    }
}
