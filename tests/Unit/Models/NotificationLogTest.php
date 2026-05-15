<?php

namespace Tests\Unit\Models;

use App\Models\NotificationLog;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        NotificationLog::create([
            'pet_id' => $pet->id, 'tutor_id' => $tutor->id,
            'type' => 'vaccine_reminder', 'channel' => 'email',
            'destination' => 'test@test.com', 'status' => 'sent', 'sent_at' => now(),
        ]);
        $this->assertDatabaseHas('notification_logs', ['pet_id' => $pet->id, 'type' => 'vaccine_reminder']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $log = NotificationLog::create(['pet_id' => $pet->id, 'type' => 'test', 'status' => 'sent', 'sent_at' => now()]);
        $this->assertInstanceOf(Pet::class, $log->pet);
    }

    public function test_tutor_relationship()
    {
        $tutor = Tutor::factory()->create();
        $log = NotificationLog::create(['tutor_id' => $tutor->id, 'type' => 'test', 'status' => 'sent', 'sent_at' => now()]);
        $this->assertInstanceOf(Tutor::class, $log->tutor);
    }
}
