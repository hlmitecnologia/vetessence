<?php

namespace Tests\Feature\Services\Nfse;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Services\Nfse\FocusNfeProvider;
use App\Services\Nfse\NfeIoProvider;
use App\Services\Nfse\NfseResult;
use App\Services\Nfse\NfseService;
use App\Services\Nfse\NfseProvider;
use App\Services\Nfse\SpedyProvider;
use App\Services\Nfse\TecnospeedProvider;
use App\Services\Nfse\WebmaniaProvider;
use Tests\ModuleTestCase;

class NfseServiceTest extends ModuleTestCase
{
    public function test_emitir_with_valid_config()
    {
        $provider = $this->createMock(NfseProvider::class);
        $provider->method('emitir')->willReturn(
            NfseResult::success('123456', 'COD123', 'https://xml', 'https://pdf', 'RPS001', 'ABCD-1234', [])
        );

        $branch = Branch::factory()->create(['municipio_ibge' => '3550308']);
        NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfse_status' => 'none',
        ]);

        $service = new NfseService($provider);
        $result = $service->emitir($invoice);

        $this->assertTrue($result->success);
        $this->assertEquals('123456', $result->nfseNumber);
        $this->assertEquals('issued', $invoice->fresh()->nfse_status);
    }

    public function test_emitir_without_config()
    {
        $provider = $this->createMock(NfseProvider::class);
        $invoice = Invoice::factory()->create(['nfse_status' => 'none']);

        $service = new NfseService($provider);
        $result = $service->emitir($invoice);

        $this->assertFalse($result->success);
        $this->assertEquals('NFS-e não configurada para o sistema.', $result->errorMessage);
    }

    public function test_emitir_without_branch_fiscal_data()
    {
        $provider = $this->createMock(NfseProvider::class);
        $branch = Branch::factory()->create(['municipio_ibge' => null]);
        NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfse_status' => 'none',
        ]);

        $service = new NfseService($provider);
        $result = $service->emitir($invoice);

        $this->assertFalse($result->success);
        $this->assertStringContainsString('Dados fiscais da unidade incompletos', $result->errorMessage);
    }

    public function test_cancelar_success()
    {
        $provider = $this->createMock(NfseProvider::class);
        $provider->method('cancelar')->willReturn(NfseResult::success('123456', '', '', '', '', '', []));

        $branch = Branch::factory()->create(['municipio_ibge' => '3550308']);
        NfseConfig::factory()->create(['is_active' => true]);
        $nfseInvoice = \App\Models\NfseInvoice::factory()->create([
            'branch_id' => $branch->id,
            'status' => 'issued',
            'issuance_date' => now()->subHours(2),
        ]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfse_status' => 'issued',
            'nfse_invoice_id' => $nfseInvoice->id,
        ]);

        $service = new NfseService($provider);
        $result = $service->cancelar($invoice, 'Cancelamento a pedido');

        $this->assertTrue($result->success);
        $this->assertEquals('cancelled', $invoice->fresh()->nfse_status);
    }

    public function test_cancelar_without_nfse()
    {
        $provider = $this->createMock(NfseProvider::class);
        $branch = Branch::factory()->create(['municipio_ibge' => '3550308']);
        NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfse_status' => 'none',
        ]);

        $service = new NfseService($provider);
        $result = $service->cancelar($invoice, 'Motivo');

        $this->assertFalse($result->success);
    }

    public function test_resolve_provider_webmania()
    {
        NfseConfig::factory()->create(['is_active' => true, 'provider' => 'webmania']);
        $service = new NfseService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');
        $config = NfseConfig::first();
        $provider = $reflection->invoke($service, $config);
        $this->assertInstanceOf(WebmaniaProvider::class, $provider);
    }

    public function test_resolve_provider_focusnfe()
    {
        NfseConfig::factory()->create(['is_active' => true, 'provider' => 'focusnfe']);
        $service = new NfseService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');
        $config = NfseConfig::first();
        $provider = $reflection->invoke($service, $config);
        $this->assertInstanceOf(FocusNfeProvider::class, $provider);
    }

    public function test_resolve_provider_spedy()
    {
        NfseConfig::factory()->create(['is_active' => true, 'provider' => 'spedy']);
        $service = new NfseService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');
        $config = NfseConfig::first();
        $provider = $reflection->invoke($service, $config);
        $this->assertInstanceOf(SpedyProvider::class, $provider);
    }

    public function test_resolve_provider_tecnospeed()
    {
        NfseConfig::factory()->create(['is_active' => true, 'provider' => 'tecnospeed']);
        $service = new NfseService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');
        $config = NfseConfig::first();
        $provider = $reflection->invoke($service, $config);
        $this->assertInstanceOf(TecnospeedProvider::class, $provider);
    }

    public function test_resolve_provider_nfeio()
    {
        NfseConfig::factory()->create(['is_active' => true, 'provider' => 'nfeio']);
        $service = new NfseService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');
        $config = NfseConfig::first();
        $provider = $reflection->invoke($service, $config);
        $this->assertInstanceOf(NfeIoProvider::class, $provider);
    }

    public function test_resolve_provider_invalid()
    {
        NfseConfig::factory()->create(['is_active' => true, 'provider' => 'invalid_provider']);
        $service = new NfseService();
        $reflection = new \ReflectionMethod($service, 'resolveProvider');
        $config = NfseConfig::first();
        $this->expectException(\InvalidArgumentException::class);
        $reflection->invoke($service, $config);
    }

    public function test_notify_tutor_creates_queue_entry()
    {
        $provider = $this->createMock(NfseProvider::class);
        $provider->method('emitir')->willReturn(
            NfseResult::success('123456', 'COD123', 'https://xml', 'https://pdf', 'RPS001', 'ABCD-1234', [])
        );

        $branch = Branch::factory()->create(['municipio_ibge' => '3550308']);
        NfseConfig::factory()->create(['is_active' => true]);
        $tutor = \App\Models\Tutor::factory()->create(['email' => 'tutor@test.com']);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'tutor_id' => $tutor->id,
            'nfse_status' => 'none',
        ]);

        $service = new NfseService($provider);
        $result = $service->emitir($invoice);

        $this->assertTrue($result->success);
        $this->assertDatabaseHas('communication_queue', [
            'tutor_id' => $tutor->id,
            'channel' => 'email',
            'status' => 'pending',
        ]);
    }

    public function test_notify_tutor_skips_without_email()
    {
        $provider = $this->createMock(NfseProvider::class);
        $provider->method('emitir')->willReturn(
            NfseResult::success('123456', 'COD123', 'https://xml', 'https://pdf', 'RPS001', 'ABCD-1234', [])
        );

        $branch = Branch::factory()->create(['municipio_ibge' => '3550308']);
        NfseConfig::factory()->create(['is_active' => true]);
        $tutor = \App\Models\Tutor::factory()->create(['email' => null]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'tutor_id' => $tutor->id,
            'nfse_status' => 'none',
        ]);

        $service = new NfseService($provider);
        $result = $service->emitir($invoice);

        $this->assertTrue($result->success);
        $this->assertDatabaseMissing('communication_queue', [
            'tutor_id' => $tutor->id,
        ]);
    }
}
