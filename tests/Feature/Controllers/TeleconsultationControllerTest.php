<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\Pet;
use App\Models\Teleconsultation;
use App\Models\User;
use Tests\ModuleTestCase;

class TeleconsultationControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        Teleconsultation::factory()->count(3)->create();
        $response = $this->get(route('teleconsultations.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        Teleconsultation::factory()->create(['status' => 'scheduled']);
        Teleconsultation::factory()->create(['status' => 'active']);

        $response = $this->get(route('teleconsultations.index', ['status' => 'scheduled']));
        $response->assertOk();
    }

    public function test_index_filters_by_search()
    {
        Pet::factory()->create(['name' => 'Rex']);
        $pet = Pet::factory()->create(['name' => 'Bolinha']);
        Teleconsultation::factory()->create(['pet_id' => $pet->id]);

        $response = $this->get(route('teleconsultations.index', ['search' => 'Bolinha']));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('teleconsultations.store'), [
            'room_name' => 'Consulta Remota Rex',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'provider' => 'jitsi',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Paciente com suspeita de alergia.',
        ]);
        $response->assertRedirect(route('teleconsultations.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('teleconsultations', [
            'room_name' => 'Consulta Remota Rex',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'provider' => 'jitsi',
            'status' => 'scheduled',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('teleconsultations.store'), []);
        $response->assertSessionHasErrors(['room_name', 'pet_id', 'vet_id', 'provider', 'scheduled_at']);
    }

    public function test_store_sets_jitsi_provider_url()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('teleconsultations.store'), [
            'room_name' => 'Jitsi Test',
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'provider' => 'jitsi',
            'scheduled_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ]);
        $response->assertRedirect();

        $teleconsultation = Teleconsultation::where('room_name', 'Jitsi Test')->first();
        $this->assertNotNull($teleconsultation);
        $this->assertNotNull($teleconsultation->room_token);
        $this->assertStringContainsString('meet.jit.si', $teleconsultation->provider_url);
    }

    public function test_show()
    {
        $teleconsultation = Teleconsultation::factory()->create();
        $response = $this->get(route('teleconsultations.show', $teleconsultation));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $teleconsultation = Teleconsultation::factory()->create([
            'room_name' => 'Original Room',
        ]);
        $newPet = Pet::factory()->create();
        $newVet = User::factory()->create();

        $response = $this->put(route('teleconsultations.update', $teleconsultation), [
            'room_name' => 'Updated Room',
            'pet_id' => $newPet->id,
            'vet_id' => $newVet->id,
            'provider' => 'jitsi',
            'scheduled_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Notas atualizadas.',
        ]);
        $response->assertRedirect(route('teleconsultations.show', $teleconsultation));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('teleconsultations', [
            'id' => $teleconsultation->id,
            'room_name' => 'Updated Room',
            'notes' => 'Notas atualizadas.',
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $teleconsultation = Teleconsultation::factory()->create();
        $response = $this->delete(route('teleconsultations.destroy', $teleconsultation));
        $response->assertRedirect(route('teleconsultations.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('teleconsultations', ['id' => $teleconsultation->id]);
    }

    public function test_start_changes_status_to_active()
    {
        $teleconsultation = Teleconsultation::factory()->create([
            'status' => 'scheduled',
            'provider_url' => 'https://meet.jit.si/test-room',
        ]);

        $response = $this->get(route('teleconsultations.start', $teleconsultation));
        $response->assertRedirect();
        $this->assertDatabaseHas('teleconsultations', [
            'id' => $teleconsultation->id,
            'status' => 'active',
        ]);
        $this->assertNotNull($teleconsultation->fresh()->started_at);
    }

    public function test_end_changes_status_to_completed()
    {
        $teleconsultation = Teleconsultation::factory()->create([
            'status' => 'active',
            'started_at' => now()->subMinutes(30),
        ]);

        $response = $this->post(route('teleconsultations.end', $teleconsultation), [
            'notes' => 'Teleconsulta encerrada. Paciente estável.',
        ]);
        $response->assertRedirect(route('teleconsultations.show', $teleconsultation));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('teleconsultations', [
            'id' => $teleconsultation->id,
            'status' => 'completed',
        ]);
        $this->assertNotNull($teleconsultation->fresh()->ended_at);
        $this->assertNotNull($teleconsultation->fresh()->duration_minutes);
    }
}
