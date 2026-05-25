<?php

namespace App\Services\Notification\WhatsApp;

use App\Services\Notification\Contracts\WhatsAppProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeniProvider implements WhatsAppProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Weni';
    }

    public function send(string $from, string $to, string $message, ?string $mediaUrl = null): NotificationResult
    {
        try {
            $apiKey = $this->config['api_key'] ?? '';
            $projectUuid = $this->config['project_uuid'] ?? '';
            $fromNumber = $this->config['from_number'] ?? $from;

            $payload = [
                'to' => preg_replace('/\D/', '', $to),
                'type' => 'text',
                'text' => ['body' => $message],
                'from' => $fromNumber,
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://api.iaweni.com.br/v1/projects/{$projectUuid}/messages", $payload);

            if ($response->successful()) {
                Log::info('Weni WhatsApp sent', ['to' => $to]);
                return NotificationResult::success($this->getName());
            }

            Log::warning('Weni WhatsApp error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "Weni error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Weni WhatsApp failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
