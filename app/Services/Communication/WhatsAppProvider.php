<?php

namespace App\Services\Communication;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppProvider implements CommunicationProvider
{
    protected string $apiUrl;
    protected string $apiToken;
    protected string $instance;

    public function __construct()
    {
        $this->apiUrl = config('communication.whatsapp.url', 'https://api.z-api.io/v1');
        $this->apiToken = config('communication.whatsapp.token', '');
        $this->instance = config('communication.whatsapp.instance', '');
    }

    public function getName(): string
    {
        return 'WhatsApp';
    }

    public function send(string $destination, string $message): bool
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->apiUrl}/instances/{$this->instance}/send-text", [
                    'phone' => $destination,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent', ['destination' => $destination]);
                return true;
            }

            Log::warning('WhatsApp API error', [
                'destination' => $destination,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp send failed', [
                'destination' => $destination,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
