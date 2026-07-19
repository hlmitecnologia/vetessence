<?php

namespace Tests\Feature\EdgeCase;

use Tests\ModuleTestCase;

class EmptyStateTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_products_index_shows_page()
    {
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Produtos');
    }

    public function test_suppliers_index_shows_page()
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertOk();
        $response->assertSee('Fornecedores');
    }

    public function test_categories_index_shows_page()
    {
        $response = $this->get(route('categories.index'));
        $response->assertOk();
        $response->assertSee('Categorias');
    }
}
