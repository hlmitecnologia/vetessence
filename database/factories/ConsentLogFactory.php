<?php

namespace Database\Factories;

use App\Models\ConsentLog;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsentLogFactory extends Factory
{
    protected $model = ConsentLog::class;

    public function definition()
    {
        return [
            'consentable_type' => Tutor::class,
            'consentable_id' => Tutor::factory(),
            'type' => 'lgpd_data_processing',
            'purpose' => 'Processamento de dados para agendamento de consultas',
            'granted' => true,
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'consented_at' => now(),
        ];
    }
}
