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
        $config->shouldReceive('getAttribute')->with('nfeio_company_id')->andReturn('company-123');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(NfeIoProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(NfeIoProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.nfeio.base_url', 'https://api.nfse.io'));

        Http::fake([
            'https://api.nfse.io/v2/companies/company-123/productinvoices' => Http::response([
                'productInvoice' => [
                    'number' => 1,
                    'accessKey' => '12345678901234567890123456789012345678901234',
                ],
            ], 201),
        ]);

        $result = $provider->emitir($config, $invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('1', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
    }

    public function test_emitir_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');
        $config->shouldReceive('getAttribute')->with('nfeio_company_id')->andReturn('company-123');
        $config->shouldReceive('getAttribute')->with('ambiente')->andReturn('homologacao');

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('id')->andReturn(123);

        $provider = \Mockery::mock(NfeIoProvider::class)->shouldAllowMockingProtectedMethods()->makePartial();
        $provider->shouldReceive('buildPayload')->andReturn(['simple' => 'payload']);

        $refBaseUrl = (new \ReflectionClass(NfeIoProvider::class))->getProperty('baseUrl');
        $refBaseUrl->setAccessible(true);
        $refBaseUrl->setValue($provider, config('nfe.nfeio.base_url', 'https://api.nfse.io'));

        Http::fake([
            'https://api.nfse.io/v2/companies/company-123/productinvoices' => Http::response([
                'errors' => [['message' => 'CNPJ do emitente inválido']],
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
        $config->shouldReceive('getAttribute')->with('nfeio_company_id')->andReturn('company-123');

        Http::fake([
            'https://api.nfse.io/v2/companies/company-123/productinvoices/invoice-001' => Http::response([
                'productInvoice' => [
                    'number' => 1,
                    'accessKey' => '12345678901234567890123456789012345678901234',
                    'flowStatus' => 'Issued',
                ],
            ], 200),
        ]);

        $result = $this->provider->consultar($config, 'invoice-001');

        $this->assertTrue($result->success);
        $this->assertEquals('1', $result->nfeNumber);
        $this->assertEquals('12345678901234567890123456789012345678901234', $result->nfeKey);
    }

    public function test_consultar_failure(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $config->shouldReceive('getAttribute')->with('nfeio_api_key')->andReturn('test-api-key');
        $config->shouldReceive('getAttribute')->with('nfeio_company_id')->andReturn('company-123');

        Http::fake([
            'https://api.nfse.io/v2/companies/company-123/productinvoices/INVALID' => Http::response([
                'errors' => [['message' => 'NF-e não encontrada']],
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
        $config->shouldReceive('getAttribute')->with('nfeio_company_id')->andReturn('company-123');

        Http::fake([
            'https://api.nfse.io/v2/companies/company-123/productinvoices/NFE-001' => Http::response([
                'productInvoice' => [
                    'number' => 1,
                    'flowStatus' => 'Cancelled',
                ],
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
        $config->shouldReceive('getAttribute')->with('nfeio_company_id')->andReturn('company-123');

        Http::fake([
            'https://api.nfse.io/v2/companies/company-123/productinvoices/NFE-001' => Http::response([
                'errors' => [['message' => 'NF-e já cancelada']],
            ], 400),
        ]);

        $result = $this->provider->cancelar($config, 'NFE-001', 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e já cancelada', $result->errorMessage);
    }
}
