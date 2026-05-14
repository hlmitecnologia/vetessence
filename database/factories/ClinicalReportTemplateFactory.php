<?php

namespace Database\Factories;

use App\Models\ClinicalReportTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClinicalReportTemplateFactory extends Factory
{
    protected $model = ClinicalReportTemplate::class;

    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'slug' => $this->faker->slug,
            'species' => $this->faker->randomElement(['canine', 'feline', null]),
            'content' => $this->faker->paragraph,
            'is_active' => true,
        ];
    }
}
