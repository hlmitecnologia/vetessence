<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\User;
use App\Models\Vaccination;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaccinationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Vaccination::create([
            'pet_id' => $pet->id,
            'vaccine' => 'Rabies',
            'batch' => 'BATCH001',
            'date' => now(),
            'next_date' => now()->addYear(),
            'vet_id' => $vet->id,
            'notes' => 'First dose',
        ]);

        $this->assertDatabaseHas('vaccinations', [
            'pet_id' => $pet->id,
            'vaccine' => 'Rabies',
            'batch' => 'BATCH001',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'vaccine' => 'Rabies',
            'batch' => 'BATCH001',
            'date' => now(),
        ]);

        $this->assertInstanceOf(Pet::class, $vaccination->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $vaccination = Vaccination::create([
            'pet_id' => $pet->id,
            'vaccine' => 'Rabies',
            'batch' => 'BATCH001',
            'date' => now(),
            'vet_id' => $vet->id,
        ]);

        $this->assertInstanceOf(User::class, $vaccination->vet);
    }
}
