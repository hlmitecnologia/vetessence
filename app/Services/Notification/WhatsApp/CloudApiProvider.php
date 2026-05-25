<?php

namespace App\Services\Notification\WhatsApp;

use App\Services\Notification\Contracts\WhatsAppProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudApiProvider implements WhatsAppProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'WhatsApp Cloud API';
    }

    public function send(string $from, string $to, string $message, ?string $mediaUrl = null): NotificationResult
    {
        try {
            $accessToken = $this->config['access_token'] ?? '';
            $phoneNumberId = $this->config['phone_number_id'] ?? '';

            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => preg_replace('/\D/', '', $to),
                'type' => 'text',
                'text' => ['body' => $message],
            ];

            $response = Http::withToken($accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://graph.facebook.com/v21.0/{$phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                $messageId = $response->json('messages.0.id');
                Log::info('Cloud API WhatsApp sent', ['to' => $to, 'messageId' => $messageId]);
                return NotificationResult::success($this->getName(), $messageId);
            }

            Log::warning('Cloud API WhatsApp error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "Cloud API error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Cloud API WhatsApp failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
