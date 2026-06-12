<?php

namespace Tests\Unit\Services\Notification\WhatsApp;

use App\Services\Notification\WhatsApp\ZapiProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class ZapiProviderTest extends ModuleTestCase
{
    private ZapiProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new ZapiProvider([
            'api_url' => 'https://api.z-api.io/v1',
            'api_token' => 'test-token',
            'instance' => 'test-instance',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Z-API', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        Http::fake([
            'https://api.z-api.io/v1/instances/test-instance/send-text' => Http::response([], 200),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Your appointment is confirmed',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Z-API', $result->provider);
    }

    public function test_send_passes_correct_payload(): void
    {
        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('5511888888888', $body['phone']);
            $this->assertEquals('Hello', $body['message']);

            return Http::response([], 200);
        });

        $this->provider->send('5511999999999', '5511888888888', 'Hello');
    }

    public function test_send_uses_default_api_url_when_not_configured(): void
    {
        $provider = new ZapiProvider([
            'api_token' => 'test-token',
            'instance' => 'test-instance',
        ]);

        Http::fake(function ($request) {
            $this->assertStringStartsWith('https://api.z-api.io/v1', $request->url());

            return Http::response([], 200);
        });

        $provider->send('from', '5511888888888', 'Test');
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://api.z-api.io/v1/instances/test-instance/send-text' => Http::response([], 401),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('Z-API', $result->provider);
        $this->assertStringContainsString('401', $result->error ?? '');
    }

    public function test_send_failure_when_exception_is_thrown(): void
    {
        Http::fake([
            'https://api.z-api.io/v1/instances/test-instance/send-text' => function () {
                throw new \Exception('Z-API timeout');
            },
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Z-API timeout', $result->error ?? '');
    }
}
