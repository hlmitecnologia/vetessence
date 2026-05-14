<?php

namespace Tests\Feature\Modules;

use App\Models\Pet;
use App\Models\TherapySession;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class TherapySessionTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('therapy-sessions.index'));
        $response->assertOk();
    }

    public function test_can_create()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->post(route('therapy-sessions.store'), [
            'pet_id' => $pet->id,
            'type' => 'physiotherapy',
            'session_date' => now()->format('Y-m-d\TH:i'),
            'status' => 'scheduled',
        ]);
        $response->assertRedirect(route('therapy-sessions.index'));
    }

    public function test_status_transition()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $session = TherapySession::factory()->create([
            'pet_id' => $pet->id,
            'status' => 'scheduled',
        ]);

        $session->update(['status' => 'completed']);

        $this->assertDatabaseHas('therapy_sessions', [
            'id' => $session->id,
            'status' => 'completed',
        ]);
    }
}
