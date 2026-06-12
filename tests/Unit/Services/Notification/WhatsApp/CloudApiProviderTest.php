<?php

namespace Tests\Unit\Services\Notification\WhatsApp;

use App\Services\Notification\WhatsApp\CloudApiProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class CloudApiProviderTest extends ModuleTestCase
{
    private CloudApiProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new CloudApiProvider([
            'access_token' => 'test-access-token',
            'phone_number_id' => '123456789',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('WhatsApp Cloud API', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        Http::fake([
            'https://graph.facebook.com/v21.0/123456789/messages' => Http::response([
                'messages' => [['id' => 'wamid.test123']],
            ], 200),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Your appointment is confirmed',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('WhatsApp Cloud API', $result->provider);
        $this->assertEquals('wamid.test123', $result->messageId);
    }

    public function test_send_strips_non_digits_from_recipient(): void
    {
        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('5511888888888', $body['to']);

            return Http::response(['messages' => [['id' => 'wamid.1']]], 200);
        });

        $this->provider->send('5511999999999', '+55 (11) 8888-8888', 'Test');
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://graph.facebook.com/v21.0/123456789/messages' => Http::response([], 400),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('WhatsApp Cloud API', $result->provider);
        $this->assertStringContainsString('400', $result->error ?? '');
    }

    public function test_send_failure_when_exception_is_thrown(): void
    {
        Http::fake([
            'https://graph.facebook.com/v21.0/123456789/messages' => function () {
                throw new \Exception('Connection timeout');
            },
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Connection timeout', $result->error ?? '');
    }
}
