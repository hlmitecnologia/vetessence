<?php

namespace Tests\Unit\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Services\Nfe\WebmaniaProvider;
use App\Services\Nfe\NfeResult;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class WebmaniaProviderTest extends ModuleTestCase
{
    protected WebmaniaProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = app(WebmaniaProvider::class);
    }

    public function test_emitir_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_app_id')->andReturn('app-id');
        $config->shouldReceive('getAttribute')->with('webmania_app_secret')->andReturn('app-secret');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(WebmaniaProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(WebmaniaProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.webmania.base_url', 'https://api.webmania.com.br/v1'));

        Http::fake([
            'https://api.webmania.com.br/v1/nfe/emitir' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://webmania.com.br/xml/nfe-001.xml',
                'pdf' => 'https://webmania.com.br/pdf/nfe-001.pdf',
                'danfe' => 'https://webmania.com.br/danfe/nfe-001.pdf',
            ], 201),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
        $this->assertEquals('https://webmania.com.br/xml/nfe-001.xml', $result->xmlUrl);
        $this->assertEquals('https://webmania.com.br/pdf/nfe-001.pdf', $result->pdfUrl);
        $this->assertEquals('https://webmania.com.br/danfe/nfe-001.pdf', $result->danfeUrl);
    }

    public function test_emitir_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_app_id')->andReturn('app-id');
        $config->shouldReceive('getAttribute')->with('webmania_app_secret')->andReturn('app-secret');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(WebmaniaProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(WebmaniaProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.webmania.base_url', 'https://api.webmania.com.br/v1'));

        Http::fake([
            'https://api.webmania.com.br/v1/nfe/emitir' => Http::response([
                'error' => 'Limite de NF-e mensal excedido',
            ], 429),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('Limite de NF-e mensal excedido', $result->errorMessage);
    }

    public function test_consultar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_app_id')->andReturn('app-id');
        $config->shouldReceive('getAttribute')->with('webmania_app_secret')->andReturn('app-secret');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');

        Http::fake([
            'https://api.webmania.com.br/v1/nfe/NFE-001' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://webmania.com.br/xml/nfe-001.xml',
                'status' => 'autorizado',
            ], 200),
        ]);

        $result = $this->provider->consultar($config, 'NFE-001');

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
    }

    public function test_consultar_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_app_id')->andReturn('app-id');
        $config->shouldReceive('getAttribute')->with('webmania_app_secret')->andReturn('app-secret');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');

        Http::fake([
            'https://api.webmania.com.br/v1/nfe/INVALID' => Http::response([
                'error' => 'NF-e não encontrada',
            ], 404),
        ]);

        $result = $this->provider->consultar($config, 'INVALID');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não encontrada', $result->errorMessage);
    }

    public function test_cancelar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_app_id')->andReturn('app-id');
        $config->shouldReceive('getAttribute')->with('webmania_app_secret')->andReturn('app-secret');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');

        Http::fake([
            'https://api.webmania.com.br/v1/nfe/NFE-001/cancelar' => Http::response([
                'status' => 'cancelado',
            ], 200),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
    }

    public function test_cancelar_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('webmania_app_id')->andReturn('app-id');
        $config->shouldReceive('getAttribute')->with('webmania_app_secret')->andReturn('app-secret');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_key')->andReturn('consumer-key');
        $config->shouldReceive('getAttribute')->with('webmania_consumer_secret')->andReturn('consumer-secret');

        Http::fake([
            'https://api.webmania.com.br/v1/nfe/NFE-001/cancelar' => Http::response([
                'error' => 'Prazo de cancelamento expirado',
            ], 400),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('Prazo de cancelamento expirado', $result->errorMessage);
    }
}
