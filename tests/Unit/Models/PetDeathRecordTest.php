<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\PetDeathRecord;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetDeathRecordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable_fields()
    {
        $pet = Pet::factory()->create();
        PetDeathRecord::create([
            'pet_id' => $pet->id,
            'death_date' => now(),
            'cause' => 'natural causes',
        ]);

        $this->assertDatabaseHas('pet_death_records', [
            'pet_id' => $pet->id,
            'cause' => 'natural causes',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $record = PetDeathRecord::create([
            'pet_id' => $pet->id,
            'death_date' => now(),
            'cause' => 'natural causes',
        ]);

        $this->assertInstanceOf(Pet::class, $record->pet);
    }
}
