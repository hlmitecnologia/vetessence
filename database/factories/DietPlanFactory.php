<?php

namespace Database\Factories;

use App\Models\DietPlan;
use App\Models\Pet;
use App\Models\MedicalRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class DietPlanFactory extends Factory
{
    protected $model = DietPlan::class;

    public function definition()
    {
        return [
            'pet_id' => Pet::factory(),
            'diet_type' => $this->faker->randomElement(['renal', 'hepatic', 'urinary', 'hypoallergenic', 'weight_management', 'gastrointestinal']),
            'brand' => $this->faker->randomElement(['Royal Canin', 'Hill\'s', 'Purina']),
            'product_name' => $this->faker->word(),
            'daily_amount' => $this->faker->randomElement(['100g', '1 sachê', '3/4 xícara']),
            'duration_days' => $this->faker->numberBetween(30, 180),
        ];
    }
}
