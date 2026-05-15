<?php

namespace Tests\Unit\Models;

use App\Models\Tutor;
use App\Models\Pet;
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
}
