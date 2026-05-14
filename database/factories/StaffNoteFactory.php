<?php

namespace Database\Factories;

use App\Models\StaffNote;
use Illuminate\Database\Eloquent\Factories\Factory;

class StaffNoteFactory extends Factory
{
    protected $model = StaffNote::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
            'priority' => 'normal',
            'created_by' => \App\Models\User::factory(),
            'is_read' => false,
        ];
    }
}
