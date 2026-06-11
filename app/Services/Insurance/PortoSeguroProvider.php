<?php

namespace App\Services\Insurance;

use App\Models\ConvenioClaim;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class PortoSeguroProvider implements InsuranceProvider
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('porto_seguro_api_key', '');
        $this->apiSecret = Setting::get('porto_seguro_api_secret', '');
        $this->baseUrl = Setting::get('porto_seguro_base_url', 'https://api.portoseguro.com.br/v1');
    }

    public function getName(): string
    {
        return 'porto-seguro';
    }

    public function submitClaim(ConvenioClaim $claim): InsuranceClaimResult
    {
        try {
            $payload = $this->buildClaimPayload($claim);

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/claims", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $externalId = $data['id'] ?? $data['claim_id'] ?? null;

                if (!$externalId) {
                    return InsuranceClaimResult::failed(
                        $this->getName(),
                        'External ID not returned by provider',
                        $data,
                    );
                }

                return InsuranceClaimResult::success(
                    $this->getName(),
                    $externalId,
                    $data,
                );
            }

            $errorMessage = $response->json('error') ?? $response->json('message') ?? $response->body();

            return InsuranceClaimResult::failed(
                $this->getName(),
                "HTTP {$response->status()}: {$errorMessage}",
                $response->json() ?? ['raw' => $response->body()],
            );
        } catch (\Throwable $e) {
            return InsuranceClaimResult::failed(
                $this->getName(),
                $e->getMessage(),
            );
        }
    }

    public function checkStatus(string $claimId): InsuranceClaimResult
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->get("{$this->baseUrl}/claims/{$claimId}");

            if ($response->successful()) {
                $data = $response->json();

                return InsuranceClaimResult::success(
                    $this->getName(),
                    $claimId,
                    $data,
                );
            }

            return InsuranceClaimResult::failed(
                $this->getName(),
                "HTTP {$response->status()}: {$response->body()}",
                $response->json() ?? ['raw' => $response->body()],
            );
        } catch (\Throwable $e) {
            return InsuranceClaimResult::failed(
                $this->getName(),
                $e->getMessage(),
            );
        }
    }

    private function buildClaimPayload(ConvenioClaim $claim): array
    {
        $convenioPet = $claim->convenioPet;
        $pet = $convenioPet?->pet;
        $convenio = $convenioPet?->convenio;

        return array_filter([
            'claim_number' => $claim->claim_number,
            'policy_number' => $convenioPet?->policy_number,
            'amount_requested' => $claim->amount_requested,
            'contract_number' => $convenio?->contract_number,
            'pet_name' => $pet?->name,
            'species' => $pet?->species,
            'breed' => $pet?->breed,
            'notes' => $claim->notes,
            'filed_at' => $claim->filed_at?->toIso8601String(),
        ], fn ($value) => $value !== null && $value !== '');
    }
}
