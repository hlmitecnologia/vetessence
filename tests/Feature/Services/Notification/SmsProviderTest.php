<?php

namespace Tests\Feature\Services\Notification;

use App\Services\Notification\Sms\SnsSmsProvider;
use App\Services\Notification\Sms\TwilioSmsProvider;
use App\Services\Notification\Sms\ZenvioSmsProvider;
use Aws\Result;
use Aws\Sns\SnsClient;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class SmsProviderTest extends TestCase
{
    public function test_twilio_send_success()
    {
        Http::fake([
            'api.twilio.com/2010-04-01/Accounts/*/Messages.json' => Http::response([
                'sid' => 'SM123',
                'status' => 'sent',
            ], 201),
        ]);

        $provider = new TwilioSmsProvider([
            'account_sid' => 'AC123',
            'auth_token' => 'token',
            'from_number' => '+15551234567',
        ]);

        $result = $provider->send('Clinic', '+15559876543', 'Your pet appointment is tomorrow');

        $this->assertTrue($result->success);
        $this->assertEquals('Twilio SMS', $result->provider);
        $this->assertEquals('SM123', $result->messageId);
    }

    public function test_twilio_send_failure()
    {
        Http::fake([
            'api.twilio.com/2010-04-01/Accounts/*/Messages.json' => Http::response(null, 401),
        ]);

        $provider = new TwilioSmsProvider([
            'account_sid' => 'AC123',
            'auth_token' => 'token',
            'from_number' => '+15551234567',
        ]);

        $result = $provider->send('Clinic', '+15559876543', 'Message');

        $this->assertFalse($result->success);
    }

    public function test_zenvio_send_success()
    {
        Http::fake([
            'api.zenvio.com.br/v1/sms/enviar' => Http::response(['id' => 'SMS-001'], 200),
        ]);

        $provider = new ZenvioSmsProvider([
            'api_key' => 'zenvio-key',
            'from_number' => '5511999999999',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Your pet vaccine is due');

        $this->assertTrue($result->success);
        $this->assertEquals('Zenvio SMS', $result->provider);
    }

    public function test_zenvio_send_failure()
    {
        Http::fake([
            'api.zenvio.com.br/v1/sms/enviar' => Http::response(null, 400),
        ]);

        $provider = new ZenvioSmsProvider([
            'api_key' => 'zenvio-key',
            'from_number' => '5511999999999',
        ]);

        $result = $provider->send('Clinic', '5511888888888', 'Message');

        $this->assertFalse($result->success);
    }

    public function test_sns_send_success()
    {
        $client = $this->getMockBuilder(SnsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['publish'])
            ->getMock();
        $client->method('publish')
            ->willReturn(new Result(['MessageId' => 'SNS-001']));

        $provider = new SnsSmsProvider([
            'key' => 'AKID',
            'secret' => 'secret',
            'region' => 'us-east-1',
        ], $client);

        $result = $provider->send('Clinic', '+5511888888888', 'Your appointment is confirmed');

        $this->assertTrue($result->success);
        $this->assertEquals('Amazon SNS', $result->provider);
        $this->assertEquals('SNS-001', $result->messageId);
    }

    public function test_sns_send_failure()
    {
        $client = $this->getMockBuilder(SnsClient::class)
            ->disableOriginalConstructor()
            ->addMethods(['publish'])
            ->getMock();
        $client->method('publish')
            ->willThrowException(new \RuntimeException('SNS error'));

        $provider = new SnsSmsProvider([
            'key' => 'AKID',
            'secret' => 'secret',
            'region' => 'us-east-1',
        ], $client);

        $result = $provider->send('Clinic', '+5511888888888', 'Message');

        $this->assertFalse($result->success);
    }
}
