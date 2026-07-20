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
            $instance = $this->config['instance'] ?? '';
            $apiToken = $this->config['api_token'] ?? '';

            if (empty($instance) || empty($apiToken)) {
                return NotificationResult::failed($this->getName(), 'Z-API: instance ou token não configurados.');
            }

            // Z-API exige apenas dígitos — remove tudo que não for número
            $phone = preg_replace('/\D/', '', $to);

            // Adiciona DDI 55 se não tiver
            if (!str_starts_with($phone, '55')) {
                $phone = '55' . $phone;
            }

            $url = "https://api.z-api.io/instances/{$instance}/token/{$apiToken}/send-text";

            $response = Http::timeout(15)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($url, [
                    'phone' => $phone,
                    'message' => $message,
                ]);

            if ($response->successful()) {
                Log::info('Z-API WhatsApp sent', ['to' => $phone]);
                return NotificationResult::success($this->getName());
            }

            $body = $response->body();
            Log::warning('Z-API WhatsApp error', [
                'to' => $phone, 'status' => $response->status(), 'body' => $body,
            ]);

            $errorMsg = $body;
            if ($response->status() === 405) {
                $errorMsg = 'Método não permitido — verifique a URL da requisição.';
            } elseif ($response->status() === 415) {
                $errorMsg = 'Content-Type inválido — use application/json.';
            } elseif ($response->status() === 401) {
                $errorMsg = 'Token ou instância inválidos — verifique as credenciais Z-API.';
            }

            return NotificationResult::failed($this->getName(), "Z-API error ({$response->status()}): {$errorMsg}");
        } catch (\Throwable $e) {
            Log::error('Z-API WhatsApp failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
