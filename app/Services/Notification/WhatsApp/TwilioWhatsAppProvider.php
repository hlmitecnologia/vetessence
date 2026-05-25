<?php

namespace App\Services\Notification\WhatsApp;

use App\Services\Notification\Contracts\WhatsAppProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioWhatsAppProvider implements WhatsAppProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Twilio WhatsApp';
    }

    public function send(string $from, string $to, string $message, ?string $mediaUrl = null): NotificationResult
    {
        try {
            $accountSid = $this->config['account_sid'] ?? '';
            $authToken = $this->config['auth_token'] ?? '';
            $fromNumber = $this->config['from_number'] ?? '';

            $payload = [
                'From' => 'whatsapp:' . $fromNumber,
                'To' => 'whatsapp:' . preg_replace('/\D/', '', $to),
                'Body' => $message,
            ];

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", $payload);

            if ($response->successful()) {
                $sid = $response->json('sid');
                Log::info('Twilio WhatsApp sent', ['to' => $to, 'sid' => $sid]);
                return NotificationResult::success($this->getName(), $sid);
            }

            Log::warning('Twilio WhatsApp error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "Twilio error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Twilio WhatsApp failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
