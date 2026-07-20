<?php

namespace App\Services\Notification\Email;

use App\Services\Notification\Contracts\EmailProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Log;
use MailerSend\Helpers\Builder\Attachment as MailerSendAttachment;
use MailerSend\Helpers\Builder\EmailParams;
use MailerSend\Helpers\Builder\Recipient;
use MailerSend\MailerSend;

class MailerSendProvider implements EmailProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'MailerSend';
    }

    public function send(string $from, string $to, string $subject, string $body, array $attachments = []): NotificationResult
    {
        try {
            $apiKey = $this->config['api_key'] ?? '';
            $fromName = $this->config['from_name'] ?? config('app.name');

            $mailersend = new MailerSend(['api_key' => $apiKey]);

            $recipients = [
                new Recipient($to, $to),
            ];

            $emailParams = (new EmailParams())
                ->setFrom($from)
                ->setFromName($fromName)
                ->setRecipients($recipients)
                ->setSubject($subject)
                ->setHtml($body);

            if (!empty($attachments)) {
                $msAttachments = [];
                foreach ($attachments as $attachment) {
                    if (isset($attachment['data'])) {
                        $msAttachments[] = new MailerSendAttachment(
                            base64_encode($attachment['data']),
                            $attachment['name'] ?? 'file',
                            'attachment',
                        );
                    } elseif (isset($attachment['path']) && file_exists($attachment['path'])) {
                        $msAttachments[] = new MailerSendAttachment(
                            base64_encode(file_get_contents($attachment['path'])),
                            $attachment['name'] ?? basename($attachment['path']),
                            'attachment',
                        );
                    }
                }
                if (!empty($msAttachments)) {
                    $emailParams->setAttachments($msAttachments);
                }
            }

            $mailersend->email->send($emailParams);

            Log::info('MailerSend email sent', ['to' => $to, 'subject' => $subject]);
            return NotificationResult::success($this->getName());
        } catch (\Throwable $e) {
            Log::error('MailerSend send failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
