<?php

namespace App\Services\Insurance;

use App\Models\ConvenioClaim;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class PetloveProvider implements InsuranceProvider
{
    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = Setting::get('petlove_api_key', '');
        $this->apiSecret = Setting::get('petlove_api_secret', '');
        $this->baseUrl = Setting::get('petlove_base_url', 'https://api.petlove.com.br/v1');
    }

    public function getName(): string
    {
        return 'petlove';
    }

    public function submitClaim(ConvenioClaim $claim): InsuranceClaimResult
    {
        try {
            $convenioPet = $claim->convenioPet;

            $payload = array_filter([
                'claim_number' => $claim->claim_number,
                'policy_number' => $convenioPet?->external_policy_id ?? $convenioPet?->policy_number,
                'amount_requested' => $claim->amount_requested,
                'pet_name' => $convenioPet?->pet?->name,
                'species' => $convenioPet?->pet?->species,
                'notes' => $claim->notes,
                'filed_at' => $claim->filed_at?->toIso8601String(),
            ], fn ($v) => $v !== null && $v !== '');

            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/claims", $payload);

            if ($response->successful()) {
                $data = $response->json();
                $externalId = $data['id'] ?? $data['claim_id'] ?? null;

                if (!$externalId) {
                    return InsuranceClaimResult::failed($this->getName(), 'External ID not returned', $data);
                }

                return InsuranceClaimResult::success($this->getName(), $externalId, $data);
            }

            return InsuranceClaimResult::failed(
                $this->getName(),
                "HTTP {$response->status()}: " . ($response->json('error') ?? $response->body()),
                $response->json() ?? ['raw' => $response->body()],
            );
        } catch (\Throwable $e) {
            return InsuranceClaimResult::failed($this->getName(), $e->getMessage());
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
                return InsuranceClaimResult::success($this->getName(), $claimId, $response->json());
            }

            return InsuranceClaimResult::failed(
                $this->getName(),
                "HTTP {$response->status()}: {$response->body()}",
                $response->json() ?? ['raw' => $response->body()],
            );
        } catch (\Throwable $e) {
            return InsuranceClaimResult::failed($this->getName(), $e->getMessage());
        }
    }

    public function checkEligibility(string $policyNumber): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
            ])->get("{$this->baseUrl}/plans/{$policyNumber}/eligibility");

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => "HTTP {$response->status()}", 'status' => 'unknown'];
        } catch (\Throwable $e) {
            return ['error' => $e->getMessage(), 'status' => 'unknown'];
        }
    }

    public function requestPreAuthorization(string $policyNumber, array $procedures): InsuranceClaimResult
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->apiKey,
                'X-API-Secret' => $this->apiSecret,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseUrl}/authorizations", [
                'policy_number' => $policyNumber,
                'procedures' => $procedures,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return InsuranceClaimResult::success(
                    $this->getName(),
                    $data['authorization_number'] ?? 'pending',
                    $data,
                );
            }

            return InsuranceClaimResult::failed(
                $this->getName(),
                "HTTP {$response->status()}: " . ($response->json('error') ?? $response->body()),
                $response->json() ?? ['raw' => $response->body()],
            );
        } catch (\Throwable $e) {
            return InsuranceClaimResult::failed($this->getName(), $e->getMessage());
        }
    }
}
