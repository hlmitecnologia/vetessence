<?php

namespace Tests\Feature\Controllers;

use Tests\ModuleTestCase;

class StockControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_movements_route_returns_200()
    {
        $response = $this->get(route('stock.movements'));
        $response->assertOk();
    }
}
