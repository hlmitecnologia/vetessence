<?php

namespace Tests\Feature\EdgeCase;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class EmptyStateTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $this->actingAs($user);
    }

    public function test_products_index_shows_empty_state()
    {
        $response = $this->get(route('products.index'));
        $response->assertOk();
        $response->assertSee('Nenhum registro encontrado.');
    }

    public function test_suppliers_index_shows_empty_state()
    {
        $response = $this->get(route('suppliers.index'));
        $response->assertOk();
        $response->assertSee('Nenhum registro encontrado.');
    }

    public function test_categories_index_shows_empty_state()
    {
        $response = $this->get(route('categories.index'));
        $response->assertOk();
        $response->assertSee('Nenhum registro encontrado.');
    }
}
