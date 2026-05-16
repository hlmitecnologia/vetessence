<?php

namespace App\Services\Communication;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsProvider implements CommunicationProvider
{
    protected string $apiUrl;
    protected string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('communication.sms.url', 'https://api.smsprovider.com/v1/send');
        $this->apiKey = config('communication.sms.key', '');
    }

    public function getName(): string
    {
        return 'SMS';
    }

    public function send(string $destination, string $message): bool
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiUrl, [
                    'to' => $destination,
                    'text' => $message,
                ]);

            if ($response->successful()) {
                Log::info('SMS sent', ['destination' => $destination]);
                return true;
            }

            Log::warning('SMS API error', [
                'destination' => $destination,
                'status' => $response->status(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('SMS send failed', [
                'destination' => $destination,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
