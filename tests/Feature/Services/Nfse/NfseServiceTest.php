<?php

namespace Tests\Feature\Services\Nfse;

use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Services\Nfse\NfseResult;
use App\Services\Nfse\NfseService;
use App\Services\Nfse\NfseProvider;
use Tests\ModuleTestCase;

class NfseServiceTest extends ModuleTestCase
{
    public function test_emitir_with_valid_config()
    {
        $provider = $this->createMock(NfseProvider::class);
        $provider->method('emitir')->willReturn(
            NfseResult::success('123456', 'COD123', 'https://xml', 'https://pdf', 'RPS001', 'ABCD-1234', [])
        );

        $config = NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $config->branch_id,
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
        $this->assertEquals('NFS-e não configurada para esta unidade.', $result->errorMessage);
    }

    public function test_cancelar_success()
    {
        $provider = $this->createMock(NfseProvider::class);
        $provider->method('cancelar')->willReturn(NfseResult::success('123456', '', '', '', '', '', []));

        $config = NfseConfig::factory()->create(['is_active' => true]);
        $nfseInvoice = \App\Models\NfseInvoice::factory()->create([
            'branch_id' => $config->branch_id,
            'status' => 'issued',
            'issuance_date' => now()->subHours(2),
        ]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $config->branch_id,
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
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'none',
        ]);

        $service = new NfseService($provider);
        $result = $service->cancelar($invoice, 'Motivo');

        $this->assertFalse($result->success);
    }
}
