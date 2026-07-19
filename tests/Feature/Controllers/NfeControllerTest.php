<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\NfeInvoice;
use App\Services\Nfe\NfeResult;
use App\Services\Nfe\NfeService;
use Tests\ModuleTestCase;

class NfeControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Branch::factory()->create();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create(['invoice_number' => 'FAT-2026-NFE1'])->id,
            'status' => 'pending',
        ]);
        NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create(['invoice_number' => 'FAT-2026-NFE2'])->id,
            'status' => 'issued',
        ]);

        $response = $this->get(route('nfe.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create(['invoice_number' => 'FAT-2026-NFE3'])->id,
            'status' => 'issued',
        ]);

        $response = $this->get(route('nfe.index', ['status' => 'issued']));
        $response->assertOk();
    }

    public function test_show()
    {
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'nfe_number' => '123456',
            'status' => 'issued',
            'issuance_date' => now(),
        ]);

        $response = $this->get(route('nfe.show', $nfeInvoice));
        $response->assertOk();
    }

    public function test_emitir_success()
    {
        $branch = Branch::factory()->create(['cnpj' => '11222333000181']);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfe_status' => 'none',
        ]);

        $this->mock(NfeService::class)
            ->shouldReceive('emitirNfce')
            ->once()
            ->andReturn(NfeResult::success(
                nfeNumber: 'NFE-123456',
                nfeKey: 'KEY-123',
                xmlUrl: 'https://xml.url',
                pdfUrl: 'https://pdf.url',
                danfeUrl: 'https://danfe.url',
                rawResponse: [],
            ));

        $response = $this->post(route('nfe.emitir', $invoice));
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_emitir_returns_error_when_service_fails()
    {
        $invoice = Invoice::factory()->create(['nfe_status' => 'none']);

        $this->mock(NfeService::class)
            ->shouldReceive('emitirNfce')
            ->once()
            ->andReturn(NfeResult::error('Erro ao emitir NF-e.'));

        $response = $this->post(route('nfe.emitir', $invoice));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_cancelar_success()
    {
        $branch = Branch::factory()->create(['cnpj' => '11222333000181']);
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => $branch->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'nfe_number' => 'NFE-123',
            'status' => 'issued',
            'issuance_date' => now()->subHours(2),
        ]);
        $invoice = Invoice::factory()->create([
            'branch_id' => $branch->id,
            'nfe_status' => 'issued',
            'nfe_invoice_id' => $nfeInvoice->id,
        ]);

        $this->mock(NfeService::class)
            ->shouldReceive('cancelar')
            ->once()
            ->andReturn(NfeResult::success(
                nfeNumber: 'NFE-123',
                nfeKey: 'KEY',
                rawResponse: [],
            ));

        $response = $this->post(route('nfe.cancelar', $invoice), [
            'motivo' => 'Cancelamento solicitado pelo tutor',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    public function test_cancelar_validates_motivo()
    {
        $invoice = Invoice::factory()->create();
        $response = $this->post(route('nfe.cancelar', $invoice), ['motivo' => '']);
        $response->assertSessionHasErrors(['motivo']);
    }

    public function test_download_xml_redirects_when_available()
    {
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'nfe_url_xml' => 'https://xml.url/nfe.xml',
            'status' => 'issued',
        ]);

        $response = $this->get(route('nfe.download-xml', $nfeInvoice));
        $response->assertRedirect();
    }

    public function test_download_xml_returns_error_when_unavailable()
    {
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'nfe_url_xml' => null,
            'status' => 'pending',
        ]);

        $response = $this->get(route('nfe.download-xml', $nfeInvoice));
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_download_pdf_redirects_when_available()
    {
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'nfe_url_pdf' => 'https://pdf.url/nfe.pdf',
            'status' => 'issued',
        ]);

        $response = $this->get(route('nfe.download-pdf', $nfeInvoice));
        $response->assertRedirect();
    }

    public function test_download_danfe_redirects_when_available()
    {
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'danfe_url' => 'https://danfe.url/danfe.pdf',
            'status' => 'issued',
        ]);

        $response = $this->get(route('nfe.download-danfe', $nfeInvoice));
        $response->assertRedirect();
    }

    public function test_export_form()
    {
        $response = $this->get(route('nfe.export-form'));
        $response->assertOk();
    }

    public function test_export()
    {
        $nfeInvoice = NfeInvoice::create([
            'branch_id' => Branch::factory()->create()->id,
            'invoice_id' => Invoice::factory()->create()->id,
            'status' => 'issued',
            'issuance_date' => now(),
        ]);

        $response = $this->post(route('nfe.export'), [
            'date_from' => now()->subMonth()->format('Y-m-d'),
            'date_to' => now()->addMonth()->format('Y-m-d'),
        ]);
        $response->assertOk();
    }

    public function test_export_validates_dates()
    {
        $response = $this->post(route('nfe.export'), [
            'date_from' => '',
            'date_to' => '',
        ]);
        $response->assertSessionHasErrors(['date_from', 'date_to']);
    }
}
