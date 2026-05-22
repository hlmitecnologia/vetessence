<?php

namespace Tests\Unit\Models;

use App\Models\City;
use App\Models\Pet;
use App\Models\State;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TutorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Tutor::factory()->create(['name' => 'Maria', 'email' => 'maria@test.com']);
        $this->assertDatabaseHas('tutors', ['email' => 'maria@test.com']);
    }

    public function test_pets_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $tutor->pets()->attach($pet->id, ['is_primary' => true, 'relationship' => 'tutor']);
        $this->assertCount(1, $tutor->pets);
    }

    public function test_primary_pets_scope()
    {
        $tutor = Tutor::factory()->create();
        $pet1 = Pet::factory()->create();
        $pet2 = Pet::factory()->create();
        $tutor->pets()->attach($pet1->id, ['is_primary' => true, 'relationship' => 'tutor']);
        $tutor->pets()->attach($pet2->id, ['is_primary' => false, 'relationship' => 'tutor']);
        $this->assertCount(1, $tutor->primaryPets);
    }

    public function test_state_relationship()
    {
        $state = State::factory()->create();
        $tutor = Tutor::factory()->create([
            'state_id' => $state->id,
            'city_id' => null,
        ]);

        $this->assertTrue($tutor->state()->first()->is($state));
    }

    public function test_city_relationship()
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);
        $tutor = Tutor::factory()->create([
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertTrue($tutor->city()->first()->is($city));
    }
}
