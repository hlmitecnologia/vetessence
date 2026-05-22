<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\State;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $state = State::create([
            'name' => 'São Paulo',
            'uf' => 'SP',
            'country' => 'BR',
        ]);

        $this->assertDatabaseHas('states', [
            'name' => 'São Paulo',
            'uf' => 'SP',
            'country' => 'BR',
        ]);
    }

    public function test_has_many_cities()
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);

        $this->assertTrue($state->cities->contains($city));
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $state->cities);
    }

    public function test_by_uf_scope()
    {
        State::create(['name' => 'São Paulo', 'uf' => 'SP']);
        State::create(['name' => 'Rio de Janeiro', 'uf' => 'RJ']);

        $result = State::byUf('SP')->first();
        $this->assertEquals('São Paulo', $result->name);
    }

    public function test_by_country_scope()
    {
        State::create(['name' => 'São Paulo', 'uf' => 'SP', 'country' => 'BR']);
        State::create(['name' => 'Buenos Aires', 'uf' => 'BA', 'country' => 'AR']);

        $result = State::byCountry('BR')->get();
        $this->assertCount(1, $result);
        $this->assertEquals('São Paulo', $result->first()->name);
    }
}
