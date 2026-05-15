<?php

namespace Tests\Unit\Models;

use App\Models\PetDeathRecord;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetDeathRecordTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $record = PetDeathRecord::create([
            'pet_id' => $pet->id,
            'death_date' => now(),
            'cause' => 'euthanasia',
            'disposition' => 'cremation',
            'cremation_type' => 'individual',
            'registered_by' => $vet->id,
            'memorial_text' => 'In loving memory',
        ]);

        $this->assertDatabaseHas('pet_death_records', [
            'pet_id' => $pet->id,
            'cause' => 'euthanasia',
            'cremation_type' => 'individual',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $record = PetDeathRecord::factory()->create(['pet_id' => $pet->id]);
        $this->assertInstanceOf(Pet::class, $record->pet);
    }

    public function test_registered_by_relationship()
    {
        $user = User::factory()->create();
        $record = PetDeathRecord::factory()->create(['registered_by' => $user->id]);
        $this->assertInstanceOf(User::class, $record->registeredBy);
    }

    public function test_cremation_scope()
    {
        PetDeathRecord::factory()->count(2)->create(['disposition' => 'cremation']);
        PetDeathRecord::factory()->count(1)->create(['disposition' => 'burial']);
        $this->assertEquals(2, PetDeathRecord::where('disposition', 'cremation')->count());
    }
}
