<?php

namespace Tests\Unit\Models;

use App\Models\DietPlan;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DietPlanTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $plan = DietPlan::create([
            'pet_id' => $pet->id,
            'diet_type' => 'renal',
            'brand' => 'Royal Canin',
            'daily_amount' => '100g',
            'duration_days' => 60,
            'instructions' => 'Mix with water',
        ]);

        $this->assertDatabaseHas('diet_plans', [
            'pet_id' => $pet->id,
            'diet_type' => 'renal',
        ]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $plan = DietPlan::factory()->create(['pet_id' => $pet->id]);
        $this->assertInstanceOf(Pet::class, $plan->pet);
    }

    public function test_diet_type_enum()
    {
        DietPlan::factory()->create(['diet_type' => 'hepatic']);
        DietPlan::factory()->create(['diet_type' => 'renal']);
        $this->assertEquals(1, DietPlan::where('diet_type', 'hepatic')->count());
    }
}
