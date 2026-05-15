<?php

namespace Tests\Unit\Models;

use App\Models\Boarding;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BoardingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        Boarding::create([
            'pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now(),
            'status' => 'checked_in', 'daily_rate' => 50.00, 'created_by' => $user->id,
        ]);
        $this->assertDatabaseHas('boardings', ['pet_id' => $pet->id, 'status' => 'checked_in']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $boarding = Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now(), 'status' => 'checked_in', 'created_by' => $user->id]);
        $this->assertInstanceOf(Pet::class, $boarding->pet);
    }

    public function test_active_scope()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now(), 'status' => 'checked_in', 'created_by' => $user->id]);
        Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now()->subDays(5), 'check_out_at' => now(), 'status' => 'checked_out', 'created_by' => $user->id]);
        $this->assertCount(1, Boarding::active()->get());
    }

    public function test_days_boarded()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $boarding = Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now()->subDays(2), 'status' => 'checked_in', 'created_by' => $user->id]);
        $this->assertGreaterThanOrEqual(3, $boarding->daysBoarded());
    }

    public function test_calculate_total()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $boarding = Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now()->subDays(2), 'status' => 'checked_in', 'daily_rate' => 50.00, 'grooming_fee' => 30.00, 'created_by' => $user->id]);
        $boarding->calculateTotal();
        $this->assertNotNull($boarding->total_amount);
    }
}
