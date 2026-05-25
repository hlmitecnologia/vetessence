<?php

namespace App\Services\Notification\Email;

use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SesProvider implements EmailProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Amazon SES';
    }

    public function send(string $from, string $to, string $subject, string $body, array $attachments = []): NotificationResult
    {
        try {
            $fromName = $this->config['from_name'] ?? config('app.name');

            config(['mail.mailers.dynamic-ses' => [
                'transport' => 'ses',
                'key' => $this->config['key'] ?? '',
                'secret' => $this->config['secret'] ?? '',
                'region' => $this->config['region'] ?? 'us-east-1',
                'timeout' => 30,
            ]]);

            Mail::mailer('dynamic-ses')
                ->alwaysFrom($from, $fromName)
                ->send(['html' => $body], [], function ($message) use ($to, $subject, $attachments) {
                    $message->to($to)->subject($subject);

                    foreach ($attachments as $attachment) {
                        if (isset($attachment['data'])) {
                            $message->attachData($attachment['data'], $attachment['name'] ?? 'file');
                        } elseif (isset($attachment['path'])) {
                            $message->attach($attachment['path'], [
                                'as' => $attachment['name'] ?? basename($attachment['path']),
                            ]);
                        }
                    }
                });

            Log::info('SES email sent', ['to' => $to, 'subject' => $subject]);
            return NotificationResult::success($this->getName());
        } catch (\Throwable $e) {
            Log::error('SES send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
