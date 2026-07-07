<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class EstoqueFlowTest extends DuskTestCase
{
    use TestsFlows;

    protected Branch $branch;
    protected User $estoque;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();

        $this->branch = Branch::factory()->create(['name' => 'Unidade Teste']);
        $this->estoque = $this->createUser('estoque', ['branch_id' => $this->branch->id]);
    }

    public function test_product_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/products')
                ->waitForText('Produtos')
                ->assertSee('Produtos');
        });
    }

    public function test_stock_movements(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/stock/movements')
                ->waitForText('Movimentações')
                ->assertSee('Movimentações de Estoque');
        });
    }

    public function test_stock_transfer(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/stock/transfer')
                ->waitForText('Transferir')
                ->assertSee('Transferir');
        });
    }

    public function test_purchase_order_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/purchase-orders')
                ->waitForText('Pedidos de Compra')
                ->assertSee('Pedidos de Compra');
        });
    }

    public function test_stock_dashboard(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/stock')
                ->waitForText('Dashboard')
                ->assertSee('Dashboard de Estoque');
        });
    }

    public function test_scanner(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/scanner')
                ->waitForText('Scanner')
                ->assertSee('Scanner');
        });
    }

    public function test_supplier_list(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/suppliers')
                ->waitForText('Fornecedores')
                ->assertSee('Fornecedores');
        });
    }

    public function test_controlled_substances(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/controlled-substances')
                ->waitForText('Substâncias Controladas')
                ->assertSee('Substâncias Controladas');
        });
    }

    public function test_pet_shop_packages(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->estoque)
                ->visit('/pet-shop-packages')
                ->waitForText('Pacotes Petshop')
                ->assertSee('Pacotes Petshop');
        });
    }
}
