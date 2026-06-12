<?php

namespace Tests\Unit\Services\Notification\Sms;

use App\Services\Notification\Sms\SnsSmsProvider;
use Aws\Result;
use Aws\Sns\SnsClient;
use Tests\ModuleTestCase;

class SnsSmsProviderTest extends ModuleTestCase
{
    private SnsSmsProvider $provider;

    private \Mockery\MockInterface $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = \Mockery::mock(SnsClient::class);

        $this->provider = new SnsSmsProvider(
            [
                'key' => 'AKIA-test',
                'secret' => 'secret',
                'region' => 'us-east-1',
            ],
            $this->client,
        );
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Amazon SNS', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        $this->client->shouldReceive('publish')
            ->once()
            ->with(\Mockery::on(function ($args) {
                $this->assertEquals('5511999999999', $args['PhoneNumber']);
                $this->assertEquals('Your appointment is confirmed', $args['Message']);
                $this->assertEquals('VetEssence', $args['MessageAttributes']['AWS.SNS.SMS.SenderID']['StringValue']);
                $this->assertEquals('Transactional', $args['MessageAttributes']['AWS.SNS.SMS.SMSType']['StringValue']);

                return true;
            }))
            ->andReturn(new Result(['MessageId' => 'sns-msg-123']));

        $result = $this->provider->send('VetEssence', '+55 (11) 99999-9999', 'Your appointment is confirmed');

        $this->assertTrue($result->success);
        $this->assertEquals('Amazon SNS', $result->provider);
        $this->assertEquals('sns-msg-123', $result->messageId);
    }

    public function test_send_strips_non_digits_from_phone(): void
    {
        $this->client->shouldReceive('publish')
            ->once()
            ->with(\Mockery::on(function ($args) {
                $this->assertEquals('5511999999999', $args['PhoneNumber']);

                return true;
            }))
            ->andReturn(new Result(['MessageId' => 'sns-456']));

        $this->provider->send('Test', '+55 (11) 99999-9999', 'Msg');
    }

    public function test_send_failure_returns_failed_result(): void
    {
        $this->client->shouldReceive('publish')
            ->once()
            ->andThrow(new \Exception('Invalid parameter'));

        $result = $this->provider->send('VetEssence', '5511999999999', 'Test');

        $this->assertFalse($result->success);
        $this->assertEquals('Amazon SNS', $result->provider);
        $this->assertStringContainsString('Invalid parameter', $result->error ?? '');
    }

    public function test_send_returns_success_without_message_id(): void
    {
        $this->client->shouldReceive('publish')
            ->once()
            ->andReturn(new Result([]));

        $result = $this->provider->send('VetEssence', '5511999999999', 'Test');

        $this->assertTrue($result->success);
        $this->assertNull($result->messageId);
    }
}
