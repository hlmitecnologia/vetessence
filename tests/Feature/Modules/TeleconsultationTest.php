<?php

namespace Tests\Feature\Modules;

use App\Models\Teleconsultation;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class TeleconsultationTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('teleconsultations.index'));
        $response->assertOk();
    }

    public function test_store()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');

        $response = $this->post(route('teleconsultations.store'), [
            'room_name' => 'Consulta Rex',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'provider' => 'jitsi',
            'scheduled_at' => now()->addDay()->format('Y-m-d\TH:i'),
        ]);
        $response->assertRedirect(route('teleconsultations.index'));
        $this->assertDatabaseHas('teleconsultations', ['room_name' => 'Consulta Rex']);
    }

    public function test_generate_room_token()
    {
        $token = Teleconsultation::generateRoomToken();
        $this->assertMatchesRegularExpression('/^[A-Z0-9-]{17}$/', $token);
    }

    public function test_start_updates_status()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');
        $tc = Teleconsultation::factory()->create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
        ]);
        $response = $this->get(route('teleconsultations.start', $tc));
        $this->assertEquals('active', $tc->fresh()->status);
    }

    public function test_end_records_duration()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');
        $tc = Teleconsultation::factory()->create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'active',
            'started_at' => now()->subMinutes(30),
        ]);
        $this->post(route('teleconsultations.end', $tc));
        $this->assertEquals('completed', $tc->fresh()->status);
        $this->assertNotNull($tc->fresh()->duration_minutes);
    }

    public function test_room_route()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');
        $tc = Teleconsultation::factory()->create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
        ]);
        $response = $this->get(route('teleconsultations.room', $tc->room_token));
        $response->assertOk();
    }
}
