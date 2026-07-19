<?php

namespace Tests\Unit\Services\Nfe;

use App\Models\Invoice;
use App\Models\NfeConfig;
use App\Models\NfeInvoice;
use App\Services\Nfe\NfeProvider;
use App\Services\Nfe\NfeResult;
use App\Services\Nfe\NfeService;
use Tests\ModuleTestCase;

class NfeServiceTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_emitir_returns_error_when_no_config(): void
    {
        $provider = \Mockery::mock(NfeProvider::class);

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn(null);

        $invoice = \Mockery::mock(Invoice::class);

        $result = $service->emitir($invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não configurada para o sistema.', $result->errorMessage);
    }

    public function test_emitir_returns_error_when_branch_has_no_cnpj(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $provider = \Mockery::mock(NfeProvider::class);

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn($config);

        $branch = \Mockery::mock(\stdClass::class);
        $branch->cnpj = null;

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('branch')->andReturn($branch);

        $result = $service->emitir($invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('Dados fiscais da unidade incompletos. Configure o CNPJ no cadastro da unidade.', $result->errorMessage);
    }

    public function test_emitir_returns_error_when_already_issued(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $provider = \Mockery::mock(NfeProvider::class);

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn($config);

        $branch = \Mockery::mock(\stdClass::class);
        $branch->cnpj = '12345678901234';

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('branch')->andReturn($branch);
        $invoice->shouldReceive('getAttribute')->with('nfe_status')->andReturn('issued');

        $result = $service->emitir($invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('Esta fatura já possui uma NF-e emitida.', $result->errorMessage);
    }

    public function test_resolve_provider_returns_injected_provider(): void
    {
        $provider = \Mockery::mock(NfeProvider::class);
        $config = \Mockery::mock(NfeConfig::class);

        $service = new \ReflectionClass(NfeService::class)->newInstance($provider);

        $reflectionMethod = new \ReflectionMethod(NfeService::class, 'resolveProvider');
        $reflectionMethod->setAccessible(true);

        $result = $reflectionMethod->invoke($service, $config);

        $this->assertSame($provider, $result);
    }

    public function test_cancelar_returns_error_when_no_config(): void
    {
        $provider = \Mockery::mock(NfeProvider::class);

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn(null);

        $invoice = \Mockery::mock(Invoice::class);

        $result = $service->cancelar($invoice, 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não configurada para o sistema.', $result->errorMessage);
    }

    public function test_cancelar_returns_error_when_no_nfe_invoice(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $provider = \Mockery::mock(NfeProvider::class);

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn($config);

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('nfeInvoice')->andReturn(null);

        $result = $service->cancelar($invoice, 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não encontrada ou já cancelada.', $result->errorMessage);
    }

    public function test_cancelar_returns_error_when_nfe_already_cancelled(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $provider = \Mockery::mock(NfeProvider::class);

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn($config);

        $nfeInvoice = \Mockery::mock(\stdClass::class);
        $nfeInvoice->status = 'cancelled';

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('nfeInvoice')->andReturn($nfeInvoice);

        $result = $service->cancelar($invoice, 'teste');

        $this->assertFalse($result->success);
        $this->assertEquals('NF-e não encontrada ou já cancelada.', $result->errorMessage);
    }

    public function test_cancelar_success(): void
    {
        $config = \Mockery::mock(NfeConfig::class);
        $provider = \Mockery::mock(NfeProvider::class);
        $provider->shouldReceive('cancelar')
            ->once()
            ->andReturn(NfeResult::success(nfeNumber: 'NFE-001', rawResponse: ['status' => 'cancelado']));

        $service = \Mockery::mock(NfeService::class, [$provider])->shouldAllowMockingProtectedMethods()->makePartial();
        $service->shouldReceive('getConfig')->andReturn($config);

        $nfeInvoice = \Mockery::mock(\stdClass::class);
        $nfeInvoice->nfe_number = 'NFE-001';
        $nfeInvoice->nfe_key = 'some-key';
        $nfeInvoice->status = 'issued';
        $nfeInvoice->shouldReceive('update')->once()->andReturn(true);

        $invoice = \Mockery::mock(Invoice::class);
        $invoice->shouldReceive('getAttribute')->with('nfeInvoice')->andReturn($nfeInvoice);
        $invoice->shouldReceive('update')->once()->andReturn(true);

        $result = $service->cancelar($invoice, 'motivo teste');

        $this->assertTrue($result->success);
        $this->assertEquals('NFE-001', $result->nfeNumber);
    }
}
