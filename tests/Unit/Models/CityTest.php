<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\State;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CityTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $state = State::factory()->create();
        $city = City::create([
            'state_id' => $state->id,
            'name' => 'São Paulo',
        ]);

        $this->assertDatabaseHas('cities', [
            'state_id' => $state->id,
            'name' => 'São Paulo',
        ]);
    }

    public function test_belongs_to_state()
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);

        $this->assertTrue($city->state->is($state));
    }

    public function test_by_state_scope()
    {
        $state1 = State::factory()->create();
        $state2 = State::factory()->create();

        City::factory()->create(['state_id' => $state1->id, 'name' => 'City A']);
        City::factory()->create(['state_id' => $state1->id, 'name' => 'City B']);
        City::factory()->create(['state_id' => $state2->id, 'name' => 'City C']);

        $cities = City::byState($state1->id)->get();
        $this->assertCount(2, $cities);
    }
}
