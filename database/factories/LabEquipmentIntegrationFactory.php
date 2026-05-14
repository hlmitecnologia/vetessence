<?php

namespace Database\Factories;

use App\Models\LabEquipmentIntegration;
use Illuminate\Database\Eloquent\Factories\Factory;

class LabEquipmentIntegrationFactory extends Factory
{
    protected $model = LabEquipmentIntegration::class;

    public function definition()
    {
        return [
            'name' => $this->faker->company . ' Analyzer',
            'equipment_type' => 'hematology',
            'protocol' => 'rest',
            'is_active' => true,
        ];
    }
}
