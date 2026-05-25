<?php

namespace App\Services\Notification\WhatsApp;

use App\Services\Notification\Contracts\WhatsAppProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZapiProvider implements WhatsAppProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Z-API';
    }

    public function send(string $from, string $to, string $message, ?string $mediaUrl = null): NotificationResult
    {
        try {
            $apiUrl = $this->config['api_url'] ?? 'https://api.z-api.io/v1';
            $apiToken = $this->config['api_token'] ?? '';
            $instance = $this->config['instance'] ?? '';

            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $apiToken,
                    'Content-Type' => 'application/json',
                ])
                ->post("{$apiUrl}/instances/{$instance}/send-text", [
                    'phone' => $to,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('Z-API WhatsApp sent', ['to' => $to]);
                return NotificationResult::success($this->getName());
            }

            Log::warning('Z-API WhatsApp error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "Z-API error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Z-API WhatsApp failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
