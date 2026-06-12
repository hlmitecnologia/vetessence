<?php

namespace Tests\Unit\Services\Notification\WhatsApp;

use App\Services\Notification\WhatsApp\WeniProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class WeniProviderTest extends ModuleTestCase
{
    private WeniProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new WeniProvider([
            'api_key' => 'test-api-key',
            'project_uuid' => 'test-project-uuid',
            'from_number' => '5511999999999',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Weni', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        Http::fake([
            'https://api.iaweni.com.br/v1/projects/test-project-uuid/messages' => Http::response([], 200),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Your appointment is confirmed',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Weni', $result->provider);
    }

    public function test_send_strips_non_digits_from_recipient(): void
    {
        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('5511888888888', $body['to']);

            return Http::response([], 200);
        });

        $this->provider->send('5511999999999', '+55 (11) 8888-8888', 'Test');
    }

    public function test_send_uses_from_number_from_config(): void
    {
        Http::fake(function ($request) {
            $body = json_decode($request->body(), true);
            $this->assertEquals('5511999999999', $body['from']);

            return Http::response([], 200);
        });

        $this->provider->send('original-from', '5511888888888', 'Test');
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://api.iaweni.com.br/v1/projects/test-project-uuid/messages' => Http::response([], 500),
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertEquals('Weni', $result->provider);
        $this->assertStringContainsString('500', $result->error ?? '');
    }

    public function test_send_failure_when_exception_is_thrown(): void
    {
        Http::fake([
            'https://api.iaweni.com.br/v1/projects/test-project-uuid/messages' => function () {
                throw new \Exception('API unreachable');
            },
        ]);

        $result = $this->provider->send(
            '5511999999999',
            '5511888888888',
            'Test',
        );

        $this->assertFalse($result->success);
        $this->assertStringContainsString('API unreachable', $result->error ?? '');
    }
}
