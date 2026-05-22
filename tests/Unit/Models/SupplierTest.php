<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\State;
use App\Models\Supplier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SupplierTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Supplier::create([
            'name' => 'Supplier A',
            'cnpj' => '00.000.000/0001-00',
            'phone' => '11999999999',
            'email' => 'supplier@test.com',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'state_id' => null,
            'city_id' => null,
            'zipcode' => null,
        ]);

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Supplier A',
            'cnpj' => '00.000.000/0001-00',
            'email' => 'supplier@test.com',
        ]);
    }

    public function test_products_relationship()
    {
        $supplier = Supplier::create([
            'name' => 'Supplier A',
            'cnpj' => '00.000.000/0001-00',
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $supplier->products);
    }

    public function test_state_relationship()
    {
        $state = State::factory()->create();
        $supplier = Supplier::create([
            'name' => 'Supplier A',
            'state_id' => $state->id,
        ]);

        $this->assertTrue($supplier->state->is($state));
    }

    public function test_city_relationship()
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);
        $supplier = Supplier::create([
            'name' => 'Supplier A',
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertTrue($supplier->city->is($city));
    }
}
