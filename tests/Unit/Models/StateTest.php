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
        $uf = strtoupper(fake()->lexify('??'));
        $state = State::create([
            'name' => 'Estado Teste',
            'uf' => $uf,
            'country' => 'ZZ',
        ]);

        $this->assertDatabaseHas('states', [
            'name' => 'Estado Teste',
            'uf' => $uf,
            'country' => 'ZZ',
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
        $uf1 = strtoupper(fake()->unique()->lexify('??'));
        $uf2 = strtoupper(fake()->unique()->lexify('??'));
        $s1 = State::create(['name' => 'Estado A', 'uf' => $uf1, 'country' => 'ZZ']);
        State::create(['name' => 'Estado B', 'uf' => $uf2, 'country' => 'ZZ']);

        $result = State::byUf($uf1)->where('id', $s1->id)->first();
        $this->assertEquals('Estado A', $result->name);
    }

    public function test_by_country_scope()
    {
        $uf1 = strtoupper(fake()->unique()->lexify('??'));
        $uf2 = strtoupper(fake()->unique()->lexify('??'));
        $s1 = State::create(['name' => 'Estado A', 'uf' => $uf1, 'country' => 'ZA']);
        State::create(['name' => 'Estado B', 'uf' => $uf2, 'country' => 'ZB']);

        $result = State::byCountry('ZA')->where('id', $s1->id)->get();
        $this->assertCount(1, $result);
        $this->assertEquals('Estado A', $result->first()->name);
    }
}
