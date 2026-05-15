<?php

namespace Tests\Unit\Models;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class InvoiceItemTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $invoice = Invoice::factory()->create();
        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consultation',
            'quantity' => 1,
            'unit_price' => 150.00,
            'total' => 150.00,
        ]);

        $this->assertDatabaseHas('invoice_items', [
            'invoice_id' => $invoice->id,
            'description' => 'Consultation',
            'quantity' => 1,
            'unit_price' => 150.00,
            'total' => 150.00,
        ]);
    }

    public function test_invoice_relationship()
    {
        $invoice = Invoice::factory()->create();
        $item = InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'description' => 'Consultation',
            'quantity' => 1,
            'unit_price' => 150.00,
            'total' => 150.00,
        ]);

        $this->assertInstanceOf(Invoice::class, $item->invoice);
    }
}
