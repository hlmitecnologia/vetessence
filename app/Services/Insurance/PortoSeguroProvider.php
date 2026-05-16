<?php

namespace App\Services\Insurance;

use App\Models\ConvenioClaim;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PortoSeguroProvider implements InsuranceProvider
{
    protected string $apiUrl;
    protected string $apiKey;
    protected int $timeout;

    public function __construct()
    {
        $this->apiUrl = config('insurance.porto_seguro.url', 'https://api.portoseguro.com.br/v1/claims');
        $this->apiKey = config('insurance.porto_seguro.key', '');
        $this->timeout = config('insurance.porto_seguro.timeout', 30);
    }

    public function getName(): string
    {
        return 'Porto Seguro';
    }

    public function submit(ConvenioClaim $claim): bool
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl . '/claims', [
                    'claim_number' => $claim->claim_number,
                    'pet_name' => $claim->convenioPet->pet->name ?? '',
                    'tutor_name' => $claim->convenioPet->pet->tutors->first()->name ?? '',
                    'amount_requested' => $claim->amount_requested,
                    'invoice_number' => $claim->invoice->invoice_number ?? '',
                    'description' => $claim->notes ?? '',
                ]);

            if ($response->successful()) {
                $body = $response->json();
                $externalId = $body['id'] ?? $body['external_id'] ?? null;
                if ($externalId) {
                    $claim->update(['external_id' => $externalId]);
                }
                Log::info('Insurance claim submitted', [
                    'provider' => $this->getName(),
                    'claim_id' => $claim->id,
                    'claim_number' => $claim->claim_number,
                    'external_id' => $externalId,
                ]);
                return true;
            }

            Log::warning('Insurance claim submission failed', [
                'provider' => $this->getName(),
                'claim_id' => $claim->id,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('Insurance claim submission error', [
                'provider' => $this->getName(),
                'claim_id' => $claim->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    public function checkStatus(string $externalId): string
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withToken($this->apiKey)
                ->get($this->apiUrl . '/claims/' . $externalId);

            if ($response->successful()) {
                $status = $response->json('status', 'unknown');
                switch ($status) {
                    case 'approved': return 'approved';
                    case 'rejected': return 'rejected';
                    case 'pending':
                    case 'under_review':
                    default: return 'filed';
                }
            }
        } catch (\Throwable $e) {
            Log::error('Insurance status check error', [
                'provider' => $this->getName(),
                'external_id' => $externalId,
                'error' => $e->getMessage(),
            ]);
        }

        return 'filed';
    }
}
