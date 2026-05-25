<?php

namespace App\Services\Notification\Sms;

use App\Services\Notification\Contracts\SmsProvider;
use App\Services\Notification\NotificationResult;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SnsSmsProvider implements SmsProvider
{
    public function __construct(
        protected array $config,
    ) {}

    public function getName(): string
    {
        return 'Amazon SNS';
    }

    public function send(string $from, string $to, string $message): NotificationResult
    {
        try {
            $key = $this->config['key'] ?? '';
            $secret = $this->config['secret'] ?? '';
            $region = $this->config['region'] ?? 'us-east-1';

            $response = Http::withHeaders([
                'X-Amz-Date' => now()->format('Ymd\THis\Z'),
                'Content-Type' => 'application/x-amz-json-1.1',
                'X-Amz-Target' => 'AmazonSimpleNotificationService.Publish',
            ])->post("https://sns.{$region}.amazonaws.com", [
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

            if ($response->successful()) {
                Log::info('SNS SMS sent', ['to' => $to]);
                return NotificationResult::success($this->getName(), $response->json('MessageId'));
            }

            Log::warning('SNS SMS error', [
                'to' => $to, 'status' => $response->status(), 'body' => $response->body(),
            ]);
            return NotificationResult::failed($this->getName(), "SNS error: {$response->status()}");
        } catch (\Throwable $e) {
            Log::error('SNS SMS failed', ['to' => $to, 'error' => $e->getMessage()]);
            return NotificationResult::failed($this->getName(), $e->getMessage());
        }
    }
}
