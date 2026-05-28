<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
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
        NfseInvoice::factory()->create([
            'invoice_id' => Invoice::factory()->create(['invoice_number' => 'FAT-2026-NFSE1'])->id,
        ]);
        NfseInvoice::factory()->create([
            'invoice_id' => Invoice::factory()->create(['invoice_number' => 'FAT-2026-NFSE2'])->id,
        ]);
        NfseInvoice::factory()->create([
            'invoice_id' => Invoice::factory()->create(['invoice_number' => 'FAT-2026-NFSE3'])->id,
        ]);
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
        $branch = Branch::factory()->create();
        NfseConfig::factory()->create(['is_active' => true]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
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
        $branch = Branch::factory()->create();
        NfseConfig::factory()->create(['is_active' => true]);
        $nfseInvoice = NfseInvoice::factory()->create([
            'branch_id' => $branch->id,
            'status' => 'issued',
            'issuance_date' => now()->subHours(2),
        ]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfse_status' => 'issued',
            'nfse_invoice_id' => $nfseInvoice->id,
        ]);

        $this->mock(NfseService::class)
            ->shouldReceive('cancelar')
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

    public function test_export_form()
    {
        $response = $this->get(route('nfse.export-form'));
        $response->assertOk();
    }

    public function test_export_returns_error_without_results()
    {
        $response = $this->post(route('nfse.export'), [
            'date_from' => '2020-01-01',
            'date_to' => '2020-01-31',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_export_validates_dates()
    {
        $response = $this->post(route('nfse.export'), [
            'date_from' => '',
            'date_to' => '',
        ]);
        $response->assertSessionHasErrors(['date_from', 'date_to']);
    }
}
