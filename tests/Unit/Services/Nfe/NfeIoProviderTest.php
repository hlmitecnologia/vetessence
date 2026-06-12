<?php

namespace Tests\Unit\Services\Nfe;

use App\Models\NfeConfig;
use App\Models\Invoice;
use App\Services\Nfe\NfeIoProvider;
use App\Services\Nfe\NfeResult;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class NfeIoProviderTest extends ModuleTestCase
{
    protected NfeIoProvider $provider;

    protected function setUp(): void
    {
        parent::setUp();

        $this->provider = app(NfeIoProvider::class);
    }

    public function test_emitir_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(NfeIoProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(NfeIoProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.nfeio.base_url', 'https://api.nfe.io'));

        Http::fake([
            'https://api.nfe.io/v1/nfe' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://nfe.io/xml/nfe-001.xml',
                'pdf' => 'https://nfe.io/pdf/nfe-001.pdf',
                'danfe' => 'https://nfe.io/danfe/nfe-001.pdf',
            ], 201),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
        $this->assertEquals('https://nfe.io/xml/nfe-001.xml', $result->xmlUrl);
        $this->assertEquals('https://nfe.io/pdf/nfe-001.pdf', $result->pdfUrl);
        $this->assertEquals('https://nfe.io/danfe/nfe-001.pdf', $result->danfeUrl);
    }

    public function test_emitir_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(NfeIoProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(NfeIoProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.nfeio.base_url', 'https://api.nfe.io'));

        Http::fake([
            'https://api.nfe.io/v1/nfe' => Http::response([
                'message' => 'CNPJ do emitente inválido',
            ], 422),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('CNPJ do emitente inválido', $result->errorMessage);
    }

    public function test_consultar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');

        Http::fake([
            'https://api.nfe.io/v1/nfe/NFE-001' => Http::response([
                'numero' => 'NFE-001',
                'chave' => '12345678901234567890123456789012345678901234',
                'xml' => 'https://nfe.io/xml/nfe-001.xml',
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
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');

        Http::fake([
            'https://api.nfe.io/v1/nfe/INVALID' => Http::response([
                'message' => 'NF-e não encontrada',
            ], 404),
        ]);

        $result = $this->provider->consultar($config, 'INVALID');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não encontrada', $result->errorMessage);
    }

    public function test_cancelar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');

        Http::fake([
            'https://api.nfe.io/v1/nfe/NFE-001/cancelar' => Http::response([
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
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');

        Http::fake([
            'https://api.nfe.io/v1/nfe/NFE-001/cancelar' => Http::response([
                'message' => 'NF-e já cancelada',
            ], 400),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e já cancelada', $result->errorMessage);
    }
}
