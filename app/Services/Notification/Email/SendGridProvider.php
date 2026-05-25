<?php

namespace App\Services\Notification\Email;

use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendGridProvider implements EmailProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'SendGrid';
    }

    public function send(string $from, string $to, string $subject, string $body, array $attachments = []): NotificationResult
    {
        try {
            $fromName = $this->config['from_name'] ?? config('app.name');
            $apiKey = $this->config['api_key'] ?? '';

            $payload = [
                'personalizations' => [
                    ['to' => [['email' => $to]], 'subject' => $subject],
                ],
                'from' => ['email' => $from, 'name' => $fromName],
                'content' => [['type' => 'text/html', 'value' => $body]],
            ];

            if (!empty($attachments)) {
                $payload['attachments'] = [];
                foreach ($attachments as $attachment) {
                    if (isset($attachment['data'])) {
                        $payload['attachments'][] = [
                            'content' => base64_encode($attachment['data']),
                            'filename' => $attachment['name'] ?? 'file',
                            'type' => $attachment['mime'] ?? 'application/octet-stream',
                        ];
                    } elseif (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $payload['attachments'][] = [
                            'content' => base64_encode(file_get_contents($attachment['path'])),
                            'filename' => $attachment['name'] ?? basename($attachment['path']),
                            'type' => mime_content_type($attachment['path']) ?: 'application/octet-stream',
                        ];
                    }
                }
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.sendgrid.com/v3/mail/send', $payload);

            if ($response->successful()) {
                Log::info('SendGrid email sent', ['to' => $to, 'subject' => $subject]);
                return NotificationResult::success($this->getName());
            }

            Log::warning('SendGrid API error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "SendGrid error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('SendGrid send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
