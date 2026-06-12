<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\TherapySession;
use Tests\ModuleTestCase;

class TherapySessionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        TherapySession::factory()->count(3)->create();
        $response = $this->get(route('therapy-sessions.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('therapy-sessions.create'));
        $response->assertOk();
    }

    public function test_store_creates_session()
    {
        $pet = Pet::factory()->create();

        $response = $this->post(route('therapy-sessions.store'), [
            'pet_id' => $pet->id,
            'type' => 'physiotherapy',
            'session_date' => now()->format('Y-m-d'),
            'duration_minutes' => 45,
            'status' => 'scheduled',
        ]);

        $response->assertRedirect(route('therapy-sessions.index'));
        $this->assertDatabaseHas('therapy_sessions', [
            'pet_id' => $pet->id,
            'type' => 'physiotherapy',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('therapy-sessions.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'type', 'session_date', 'status']);
    }

    public function test_show()
    {
        $session = TherapySession::factory()->create();
        $response = $this->get(route('therapy-sessions.show', $session));
        $response->assertOk();
    }

    public function test_edit()
    {
        $session = TherapySession::factory()->create();
        $response = $this->get(route('therapy-sessions.edit', $session));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $session = TherapySession::factory()->create(['duration_minutes' => 30]);

        $response = $this->put(route('therapy-sessions.update', $session), [
            'pet_id' => $session->pet_id,
            'type' => $session->type,
            'session_date' => $session->session_date->format('Y-m-d'),
            'duration_minutes' => 60,
            'status' => $session->status,
        ]);

        $response->assertRedirect(route('therapy-sessions.index'));
        $this->assertDatabaseHas('therapy_sessions', [
            'id' => $session->id,
            'duration_minutes' => 60,
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $session = TherapySession::factory()->create();

        $response = $this->delete(route('therapy-sessions.destroy', $session));

        $response->assertRedirect(route('therapy-sessions.index'));
        $this->assertDatabaseMissing('therapy_sessions', ['id' => $session->id]);
    }
}
