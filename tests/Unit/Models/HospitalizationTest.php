<?php

namespace Tests\Unit\Models;

use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HospitalizationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        Hospitalization::create([
            'pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'vet_id' => $vet->id,
            'admission_date' => now(), 'admission_reason' => 'Cirurgia', 'status' => 'admitted',
        ]);
        $this->assertDatabaseHas('hospitalizations', ['pet_id' => $pet->id, 'status' => 'admitted']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $this->assertInstanceOf(Pet::class, $h->pet);
    }

    public function test_tutor_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'admission_date' => now(), 'status' => 'admitted']);
        $this->assertInstanceOf(Tutor::class, $h->tutor);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $h = Hospitalization::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'admission_date' => now(), 'status' => 'admitted']);
        $this->assertInstanceOf(User::class, $h->vet);
    }
}
