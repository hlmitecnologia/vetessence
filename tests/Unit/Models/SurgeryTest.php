<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\Surgery;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SurgeryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        Surgery::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'scheduled_date' => now(),
            'surgery_type' => 'castration',
            'status' => 'scheduled',
        ]);

        $this->assertDatabaseHas('surgeries', [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'surgery_type' => 'castration',
            'status' => 'scheduled',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'scheduled_date' => now(),
            'surgery_type' => 'castration',
            'status' => 'scheduled',
        ]);

        $this->assertInstanceOf(Pet::class, $surgery->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $surgery = Surgery::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'scheduled_date' => now(),
            'surgery_type' => 'castration',
            'status' => 'scheduled',
        ]);

        $this->assertInstanceOf(User::class, $surgery->vet);
    }
}
