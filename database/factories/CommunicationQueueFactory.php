<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\CommunicationQueue;
use App\Models\Tutor;
use Illuminate\Database\Eloquent\Factories\Factory;

class CommunicationQueueFactory extends Factory
{
    protected $model = CommunicationQueue::class;

    public function definition()
    {
        return [
            'tutor_id' => Tutor::factory(),
            'channel' => $this->faker->randomElement(['email', 'sms', 'whatsapp']),
            'destination' => $this->faker->email(),
            'message_content' => $this->faker->sentence(),
            'status' => 'pending',
            'scheduled_at' => now(),
            'branch_id' => Branch::factory(),
        ];
    }
}
