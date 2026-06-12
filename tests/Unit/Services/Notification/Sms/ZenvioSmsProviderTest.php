<?php

namespace Tests\Unit\Services\Notification\Sms;

use App\Services\Notification\Sms\ZenvioSmsProvider;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class ZenvioSmsProviderTest extends ModuleTestCase
{
    private ZenvioSmsProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = new ZenvioSmsProvider([
            'api_key' => 'zenvio-key-123',
        ]);
    }

    public function test_get_name(): void
    {
        $this->assertEquals('Zenvio SMS', $this->provider->getName());
    }

    public function test_send_success(): void
    {
        Http::fake([
            'https://api.zenvio.com.br/v1/sms/enviar' => Http::response([], 200),
        ]);

        $result = $this->provider->send(
            'VetEssence',
            '+55 (11) 99999-9999',
            'Sua consulta foi confirmada',
        );

        $this->assertTrue($result->success);
        $this->assertEquals('Zenvio SMS', $result->provider);
    }

    public function test_send_failure_when_api_returns_error(): void
    {
        Http::fake([
            'https://api.zenvio.com.br/v1/sms/enviar' => Http::response([], 401),
        ]);

        $result = $this->provider->send('VetEssence', '+5511999999999', 'Test');

        $this->assertFalse($result->success);
        $this->assertEquals('Zenvio SMS', $result->provider);
        $this->assertStringContainsString('401', $result->error ?? '');
    }

    public function test_send_strips_non_digits_from_phone(): void
    {
        Http::fake([
            'https://api.zenvio.com.br/v1/sms/enviar' => function ($request) {
                $this->assertEquals('5511999999999', $request['numero_destino']);

                return Http::response([], 200);
            },
        ]);

        $this->provider->send('VetEssence', '+55 (11) 99999-9999', 'Msg');
    }

    public function test_send_sets_correct_headers(): void
    {
        Http::fake([
            'https://api.zenvio.com.br/v1/sms/enviar' => function ($request) {
                $this->assertStringStartsWith('Bearer zenvio', $request->header('Authorization')[0]);
                $this->assertEquals('application/json', $request->header('Content-Type')[0]);

                return Http::response([], 200);
            },
        ]);

        $this->provider->send('VetEssence', '+5511999999999', 'Msg');
    }

    public function test_send_failure_throws_exception(): void
    {
        Http::fake([
            'https://api.zenvio.com.br/v1/sms/enviar' => function () {
                throw new \Exception('API timeout');
            },
        ]);

        $result = $this->provider->send('VetEssence', '+5511999999999', 'Test');

        $this->assertFalse($result->success);
        $this->assertStringContainsString('API timeout', $result->error ?? '');
    }
}
