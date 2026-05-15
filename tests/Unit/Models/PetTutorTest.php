<?php

namespace Tests\Unit\Models;

use App\Models\PetTutor;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetTutorTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        PetTutor::create(['pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'is_primary' => true, 'relationship' => 'tutor']);
        $this->assertDatabaseHas('pet_tutor', ['pet_id' => $pet->id, 'is_primary' => true]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $pt = PetTutor::create(['pet_id' => $pet->id, 'tutor_id' => $tutor->id]);
        $this->assertInstanceOf(Pet::class, $pt->pet);
    }

    public function test_tutor_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $pt = PetTutor::create(['pet_id' => $pet->id, 'tutor_id' => $tutor->id]);
        $this->assertInstanceOf(Tutor::class, $pt->tutor);
    }
}
