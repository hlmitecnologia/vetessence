<?php

namespace Tests\Unit\Models;

use App\Models\Branch;
use App\Models\Invoice;
use App\Models\NfseInvoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NfseInvoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $nfse = NfseInvoice::factory()->create([
            'nfse_number' => '123456',
            'status' => 'issued',
        ]);

        $this->assertDatabaseHas('nfse_invoices', [
            'id' => $nfse->id,
            'nfse_number' => '123456',
            'status' => 'issued',
        ]);
    }

    public function test_issuance_date_cast()
    {
        $nfse = NfseInvoice::factory()->create(['issuance_date' => '2026-05-20 10:00:00']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $nfse->issuance_date);
    }

    public function test_branch_relationship()
    {
        $branch = Branch::factory()->create();
        $nfse = NfseInvoice::factory()->create(['branch_id' => $branch->id]);
        $this->assertInstanceOf(Branch::class, $nfse->branch);
        $this->assertEquals($branch->id, $nfse->branch->id);
    }

    public function test_invoice_relationship()
    {
        $invoice = Invoice::factory()->create();
        $nfse = NfseInvoice::factory()->create(['invoice_id' => $invoice->id]);
        $this->assertInstanceOf(Invoice::class, $nfse->invoice);
        $this->assertEquals($invoice->id, $nfse->invoice->id);
    }

    public function test_pending_state()
    {
        $nfse = NfseInvoice::factory()->pending()->create();
        $this->assertEquals('pending', $nfse->status);
        $this->assertNull($nfse->nfse_number);
        $this->assertNull($nfse->issuance_date);
    }

    public function test_cancelled_state()
    {
        $nfse = NfseInvoice::factory()->cancelled()->create();
        $this->assertEquals('cancelled', $nfse->status);
        $this->assertNotNull($nfse->cancelled_at);
    }
}
