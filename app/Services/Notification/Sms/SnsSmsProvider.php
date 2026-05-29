<?php

namespace App\Services\Notification\Sms;

use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\NotificationResult;
use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Log;

class SnsSmsProvider implements SmsProvider
{
    protected SnsClient $client;

    public function __construct(
        protected array $config,
    ) {
        $this->client = new SnsClient([
            'version' => 'latest',
            'region' => $this->config['region'] ?? 'us-east-1',
            'credentials' => [
                'key' => $this->config['key'] ?? '',
                'secret' => $this->config['secret'] ?? '',
            ],
            'http' => [
                'timeout' => 10,
                'connect_timeout' => 5,
            ],
        ]);
    }

    public function getName(): string
    {
        return 'Amazon SNS';
    }

    public function send(string $from, string $to, string $message): NotificationResult
    {
        try {
            $result = $this->client->publish([
                'PhoneNumber' => preg_replace('/\D/', '', $to),
                'Message' => $message,
                'MessageAttributes' => [
                    'AWS.SNS.SMS.SenderID' => [
                        'DataType' => 'String',
                        'StringValue' => $from,
                    ],
                    'AWS.SNS.SMS.SMSType' => [
                        'DataType' => 'String',
                        'StringValue' => 'Transactional',
                    ],
                ],
            ]);

            $messageId = $result->get('MessageId');

            Log::info('SNS SMS sent', ['to' => $to, 'message_id' => $messageId]);

            return NotificationResult::success($this->getName(), $messageId);
        } catch (\Throwable $e) {
            Log::error('SNS SMS failed', ['to' => $to, 'error' => $e->getMessage()]);

            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
