<?php

namespace App\Services\Notification\Sms;

use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZenvioSmsProvider implements SmsProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Zenvio SMS';
    }

    public function send(string $from, string $to, string $message): NotificationResult
    {
        try {
            $apiKey = $this->config['api_key'] ?? '';

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.zenvio.com.br/v1/sms/enviar', [
                'numero_destino' => preg_replace('/\D/', '', $to),
                'texto' => $message,
                'identificador' => $from,
            ]);

            if ($response->successful()) {
                Log::info('Zenvio SMS sent', ['to' => $to]);
                return NotificationResult::success($this->getName());
            }

            Log::warning('Zenvio SMS error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "Zenvio error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Zenvio SMS failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
