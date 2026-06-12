<?php

namespace Tests\Unit\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Services\Nfe\FocusNfeProvider;
use App\Services\Nfe\NfeResult;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class FocusNfeProviderTest extends ModuleTestCase
{
    protected FocusNfeProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = app(FocusNfeProvider::class);
    }

    public function test_emitir_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('focusnfe_token')->andReturn('test-token');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(FocusNfeProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(FocusNfeProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.focusnfe.base_url', 'https://api.focusnfe.com.br'));

        Http::fake([
            'https://api.focusnfe.com.br/v2/nfe?ref=123' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://focusnfe.com.br/xml/nfe-001.xml',
                'pdf' => 'https://focusnfe.com.br/pdf/nfe-001.pdf',
                'danfe' => 'https://focusnfe.com.br/danfe/nfe-001.pdf',
            ], 201),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
        $this->assertEquals('https://focusnfe.com.br/xml/nfe-001.xml', $result->xmlUrl);
        $this->assertEquals('https://focusnfe.com.br/pdf/nfe-001.pdf', $result->pdfUrl);
        $this->assertEquals('https://focusnfe.com.br/danfe/nfe-001.pdf', $result->danfeUrl);
    }

    public function test_emitir_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('focusnfe_token')->andReturn('test-token');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(FocusNfeProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(FocusNfeProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.focusnfe.base_url', 'https://api.focusnfe.com.br'));

        Http::fake([
            'https://api.focusnfe.com.br/v2/nfe?ref=123' => Http::response([
                'erro' => 'CPF do destinatário inválido',
            ], 422),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('CPF do destinatário inválido', $result->errorMessage);
        $this->assertNotNull($result->rawResponse);
    }

    public function test_consultar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('focusnfe_token')->andReturn('test-token');

        Http::fake([
            'https://api.focusnfe.com.br/v2/nfe/NFE-001' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://focusnfe.com.br/xml/nfe-001.xml',
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
        $config->shouldReceive('getAttribute')->with('focusnfe_token')->andReturn('test-token');

        Http::fake([
            'https://api.focusnfe.com.br/v2/nfe/INVALID' => Http::response([
                'erro' => 'NF-e não encontrada',
            ], 404),
        ]);

        $result = $this->provider->consultar($config, 'INVALID');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não encontrada', $result->errorMessage);
    }

    public function test_cancelar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('focusnfe_token')->andReturn('test-token');

        Http::fake([
            'https://api.focusnfe.com.br/v2/nfe/NFE-001?motivo=teste' => Http::response([
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
        $config->shouldReceive('getAttribute')->with('focusnfe_token')->andReturn('test-token');

        Http::fake([
            'https://api.focusnfe.com.br/v2/nfe/NFE-001?motivo=teste' => Http::response([
                'erro' => 'NF-e já cancelada',
            ], 400),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e já cancelada', $result->errorMessage);
    }
}
