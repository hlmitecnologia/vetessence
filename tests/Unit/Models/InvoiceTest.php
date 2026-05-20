<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\NfseInvoice;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        Invoice::create([
            'invoice_number' => 'FAT-2024-000001',
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 100.50,
            'discount' => 10.00,
            'total' => 90.50,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'FAT-2024-000001',
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'status' => 'pending',
        ]);
    }

    public function test_pet_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $invoice = Invoice::create([
            'invoice_number' => 'FAT-2024-000002',
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 100,
            'total' => 100,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertInstanceOf(Pet::class, $invoice->pet);
    }

    public function test_tutor_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $invoice = Invoice::create([
            'invoice_number' => 'FAT-2024-000003',
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 100,
            'total' => 100,
            'status' => 'pending',
            'due_date' => now(),
        ]);

        $this->assertInstanceOf(Tutor::class, $invoice->tutor);
    }

    public function test_items_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $invoice = Invoice::create([
            'invoice_number' => 'FAT-2024-000004',
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 100,
            'total' => 100,
            'status' => 'pending',
            'due_date' => now(),
        ]);
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Service',
            'quantity' => 1,
            'unit_price' => 100.00,
            'total' => 100.00,
        ]);

        $this->assertCount(1, $invoice->items);
        $this->assertInstanceOf(InvoiceItem::class, $invoice->items->first());
    }

    public function test_generateNumber()
    {
        $number = Invoice::generateNumber();

        $this->assertStringContainsString('FAT', $number);
        $this->assertStringContainsString(date('Y'), $number);
    }

    public function test_nfse_invoice_relationship()
    {
        $nfseInvoice = NfseInvoice::factory()->create();
        $invoice = Invoice::factory()->create(['nfse_invoice_id' => $nfseInvoice->id]);
        $this->assertInstanceOf(NfseInvoice::class, $invoice->nfseInvoice);
        $this->assertEquals($nfseInvoice->id, $invoice->nfseInvoice->id);
    }

    public function test_nfse_status_fillable()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        Invoice::create([
            'invoice_number' => 'FAT-2026-NFSE',
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'subtotal' => 100,
            'total' => 100,
            'status' => 'pending',
            'due_date' => now(),
            'nfse_status' => 'none',
        ]);

        $this->assertDatabaseHas('invoices', [
            'invoice_number' => 'FAT-2026-NFSE',
            'nfse_status' => 'none',
        ]);
    }
}
