<?php

namespace App\Services\Notification\Sms;

use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TwilioSmsProvider implements SmsProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Twilio SMS';
    }

    public function send(string $from, string $to, string $message): NotificationResult
    {
        try {
            $accountSid = $this->config['account_sid'] ?? '';
            $authToken = $this->config['auth_token'] ?? '';

            $response = Http::withBasicAuth($accountSid, $authToken)
                ->asForm()
                ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                    'From' => $this->config['from_number'] ?? $from,
                    'To' => $to,
                    'Body' => $message,
                ]);

            if ($response->successful()) {
                $sid = $response->json('sid');
                Log::info('Twilio SMS sent', ['to' => $to, 'sid' => $sid]);
                return NotificationResult::success($this->getName(), $sid);
            }

            Log::warning('Twilio SMS error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "Twilio error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('Twilio SMS failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
