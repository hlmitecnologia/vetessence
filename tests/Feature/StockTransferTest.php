<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Branch;
use App\Models\StockMovement;
use Tests\ModuleTestCase;

class StockTransferTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('estoque');
    }

    public function test_transfer_form_loads()
    {
        Branch::factory()->count(2)->create();
        $response = $this->get(route('stock.transfer-form'));
        $response->assertOk();
    }

    public function test_transfer_creates_movements()
    {
        $from = Branch::factory()->create();
        $to = Branch::factory()->create();
        $product = Product::factory()->create();

        $response = $this->post(route('stock.transfer'), [
            'product_id' => $product->id,
            'quantity' => 10,
            'from_branch_id' => $from->id,
            'to_branch_id' => $to->id,
            'notes' => 'Teste',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => 10,
            'type' => 'transfer_out',
            'branch_id' => $from->id,
        ]);
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'quantity' => 10,
            'type' => 'transfer_in',
            'branch_id' => $to->id,
        ]);
    }
}
