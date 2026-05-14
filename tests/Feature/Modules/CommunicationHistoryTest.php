<?php

namespace Tests\Feature\Modules;

use App\Models\NotificationLog;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class CommunicationHistoryTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_communication_page()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        NotificationLog::create([
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'type' => 'vaccination_reminder',
            'channel' => 'email',
            'destination' => 'test@test.com',
            'message' => 'Test',
            'sent_at' => now(),
            'status' => 'pending',
        ]);

        $response = $this->get(route('tutors.communication', $tutor));
        $response->assertOk();
    }
}
