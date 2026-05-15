<?php

namespace Tests\Unit\Models;

use App\Models\ControlledSubstanceLog;
use App\Models\ControlledSubstance;
use App\Models\User;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ControlledSubstanceLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $substance = ControlledSubstance::create(['name' => 'Cetamina', 'schedule' => 'C1', 'unit' => 'ml']);
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        ControlledSubstanceLog::create([
            'controlled_substance_id' => $substance->id, 'user_id' => $user->id, 'pet_id' => $pet->id,
            'type' => 'dispensation', 'quantity' => 10, 'balance_before' => 100, 'balance_after' => 90,
            'reason' => 'Uso cirúrgico',
        ]);
        $this->assertDatabaseHas('controlled_substance_logs', ['controlled_substance_id' => $substance->id, 'quantity' => 10]);
    }

    public function test_substance_relationship()
    {
        $substance = ControlledSubstance::create(['name' => 'Cetamina', 'schedule' => 'C1', 'unit' => 'ml']);
        $log = ControlledSubstanceLog::create(['controlled_substance_id' => $substance->id, 'type' => 'dispensation', 'quantity' => 5, 'balance_before' => 100, 'balance_after' => 95, 'reason' => 'test']);
        $this->assertInstanceOf(ControlledSubstance::class, $log->substance);
    }

    public function test_user_relationship()
    {
        $pet = Pet::factory()->create();
        $substance = ControlledSubstance::create(['name' => 'Cetamina', 'schedule' => 'C1', 'unit' => 'ml']);
        $user = User::factory()->create();
        $log = ControlledSubstanceLog::create(['controlled_substance_id' => $substance->id, 'user_id' => $user->id, 'pet_id' => $pet->id, 'type' => 'dispensation', 'quantity' => 5, 'balance_before' => 100, 'balance_after' => 95, 'reason' => 'test']);
        $this->assertInstanceOf(User::class, $log->user);
    }
}
