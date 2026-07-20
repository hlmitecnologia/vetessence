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
            'https://api.z-api.io/instances/test-instance/token/test-token/send-text' => Http::response([], 200),
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

            $url = $request->url();
            $this->assertStringContainsString('test-instance', $url);
            $this->assertStringContainsString('test-token', $url);

            return Http::response([], 200);
        });

        $this->provider->send('5511999999999', '5511888888888', 'Hello');
    }

    public function test_send_cleans_phone_formatting_and_adds_55(): void
    {
        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('5511998464769', $body['phone']);
            return Http::response([], 200);
        });

        $this->provider->send('from', '(11) 99846-4769', 'Test');
    }

    public function test_send_skips_55_if_already_present(): void
    {
        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('5511998464769', $body['phone']);
            return Http::response([], 200);
        });

        $this->provider->send('from', '5511998464769', 'Test');
    }

    public function test_send_failure_when_credentials_missing(): void
    {
        $provider = new ZapiProvider(['api_token' => '', 'instance' => '']);

        $result = $provider->send('from', '5511888888888', 'Test');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('não configurados', $result->error ?? '');
    }

    public function test_send_failure_when_api_returns_401(): void
    {
        Http::fake([
            'https://api.z-api.io/instances/test-instance/token/test-token/send-text' => Http::response([], 401),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertStringContainsString('401', $result->error ?? '');
        $this->assertStringContainsString('verifique as credenciais', $result->error ?? '');
    }

    public function test_send_failure_when_exception_is_thrown(): void
    {
        Http::fake([
            'https://api.z-api.io/instances/test-instance/token/test-token/send-text' => function () {
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
