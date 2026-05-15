<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\WeightRecord;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class WeightRecordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        WeightRecord::create([
            'pet_id' => $pet->id,
            'weight' => 25.50,
            'bcs' => 5,
            'measurement_date' => now(),
        ]);

        $this->assertDatabaseHas('weight_records', [
            'pet_id' => $pet->id,
            'weight' => 25.50,
            'bcs' => 5,
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $record = WeightRecord::create([
            'pet_id' => $pet->id,
            'weight' => 25.50,
            'measurement_date' => now(),
        ]);

        $this->assertInstanceOf(Pet::class, $record->pet);
    }
}
