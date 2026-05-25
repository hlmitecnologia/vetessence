<?php

namespace App\Services\Notification\Email;

use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SmtpProvider implements EmailProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'SMTP';
    }

    public function send(string $from, string $to, string $subject, string $body, array $attachments = []): NotificationResult
    {
        try {
            $fromName = $this->config['from_name'] ?? config('app.name');

            config(['mail.mailers.dynamic-smtp' => [
                'transport' => 'smtp',
                'host' => $this->config['host'] ?? '',
                'port' => (int) ($this->config['port'] ?? 587),
                'username' => $this->config['username'] ?? '',
                'password' => $this->config['password'] ?? '',
                'encryption' => $this->config['encryption'] ?? 'tls',
                'timeout' => 30,
            ]]);

            Mail::mailer('dynamic-smtp')
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

            Log::info('SMTP email sent', ['to' => $to, 'subject' => $subject]);
            return NotificationResult::success($this->getName());
        } catch (\Throwable $e) {
            Log::error('SMTP send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
