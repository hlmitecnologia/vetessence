<?php

namespace Tests\Feature\Controllers;

use App\Models\Invoice;
use App\Models\NfseConfig;
use App\Models\NfseInvoice;
use App\Services\Nfse\NfseResult;
use App\Services\Nfse\NfseService;
use Tests\ModuleTestCase;

class NfseControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        NfseInvoice::factory()->count(3)->create();
        $response = $this->get(route('nfse.index'));
        $response->assertOk();
    }

    public function test_show()
    {
        $nfseInvoice = NfseInvoice::factory()->create();
        $response = $this->get(route('nfse.show', $nfseInvoice));
        $response->assertOk();
    }

    public function test_emitir_success()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'none',
        ]);

        $this->mock(NfseService::class)
            ->shouldReceive('emitir')
            ->once()
            ->andReturn(NfseResult::success(
                nfseNumber: '123456',
                nfseCode: 'COD',
                xmlUrl: 'https://xml',
                pdfUrl: 'https://pdf',
                rpsNumber: 'RPS',
                verificationCode: 'CODE',
                rawResponse: [],
            ));

        $response = $this->post(route('nfse.emitir', $invoice));
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_emitir_without_config()
    {
        $invoice = Invoice::factory()->create(['nfse_status' => 'none']);

        $response = $this->post(route('nfse.emitir', $invoice));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cancelar_success()
    {
        $config = NfseConfig::factory()->create(['is_active' => true]);
        $nfseInvoice = NfseInvoice::factory()->create([
            'branch_id' => $config->branch_id,
            'status' => 'issued',
            'issuance_date' => now()->subHours(2),
        ]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $config->branch_id,
            'nfse_status' => 'issued',
            'nfse_invoice_id' => $nfseInvoice->id,
        ]);

        $this->mock(NfseService::class)
            ->shouldReceive('cancelar')
            ->once()
            ->andReturn(NfseResult::success('123456', rawResponse: []));

        $response = $this->post(route('nfse.cancelar', $invoice), [
            'motivo' => 'Cancelamento a pedido',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_cancelar_validates_motivo()
    {
        $invoice = Invoice::factory()->create();
        $response = $this->post(route('nfse.cancelar', $invoice), ['motivo' => '']);
        $response->assertSessionHasErrors(['motivo']);
    }
}
