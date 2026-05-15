<?php

namespace Tests\Feature\Controllers;

use App\Models\AnesthesiaMonitoring;
use App\Models\Pet;
use App\Models\Surgery;
use App\Models\User;
use Tests\ModuleTestCase;

class AnesthesiaMonitoringControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('anesthesia-monitorings.index'));
        $response->assertOk();
    }

    public function test_store_creates_monitoring()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $surgery = Surgery::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'surgery_type' => 'castração',
            'scheduled_date' => now(),
            'status' => 'scheduled',
        ]);

        $response = $this->post(route('anesthesia-monitorings.store'), [
            'surgery_id' => $surgery->id,
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'monitoring_start' => now(),
            'premedication' => 'Acepromazina',
            'induction_agent' => 'Propofol',
            'maintenance_agent' => 'Isoflurano',
            'observations' => 'Paciente estável',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('anesthesia_monitorings', [
            'pet_id' => $pet->id,
            'surgery_id' => $surgery->id,
        ]);
    }

    public function test_show()
    {
        $monitoring = AnesthesiaMonitoring::factory()->create();

        $response = $this->get(route('anesthesia-monitorings.show', $monitoring));
        $response->assertOk();
    }

    public function test_update()
    {
        $monitoring = AnesthesiaMonitoring::factory()->create();

        $response = $this->put(route('anesthesia-monitorings.update', $monitoring), [
            'surgery_id' => $monitoring->surgery_id,
            'pet_id' => $monitoring->pet_id,
            'vet_id' => $monitoring->vet_id,
            'observations' => 'Observação atualizada',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('anesthesia_monitorings', [
            'id' => $monitoring->id,
            'observations' => 'Observação atualizada',
        ]);
    }

    public function test_destroy()
    {
        $monitoring = AnesthesiaMonitoring::factory()->create();

        $response = $this->delete(route('anesthesia-monitorings.destroy', $monitoring));
        $response->assertRedirect();
        $this->assertDatabaseMissing('anesthesia_monitorings', ['id' => $monitoring->id]);
    }
}
