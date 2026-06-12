<?php

namespace Tests\Feature\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Pet;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Tests\ModuleTestCase;

class AppointmentControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Branch::factory()->create();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        Appointment::factory()->count(3)->create();
        $response = $this->get(route('appointments.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        Appointment::factory()->create(['status' => 'scheduled']);
        Appointment::factory()->create(['status' => 'completed']);

        $response = $this->get(route('appointments.index', ['status' => 'scheduled']));
        $response->assertOk();
    }

    public function test_index_filters_by_date()
    {
        Appointment::factory()->create(['date' => today()]);
        $response = $this->get(route('appointments.index', ['date' => today()->format('Y-m-d')]));
        $response->assertOk();
    }

    public function test_create()
    {
        Pet::factory()->create();
        Service::factory()->create();
        $response = $this->get(route('appointments.create'));
        $response->assertOk();
    }

    public function test_store_creates_appointment()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $service = Service::factory()->create();

        $response = $this->post(route('appointments.store'), [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => today()->addDays(3)->format('Y-m-d'),
            'time' => '14:30',
            'type' => 'consulta',
            'reason' => 'Check-up geral',
            'services' => [$service->id],
        ]);
        $response->assertRedirect(route('appointments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('appointments', [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'status' => 'scheduled',
            'type' => 'consulta',
        ]);
        $this->assertDatabaseHas('appointment_services', [
            'service_id' => $service->id,
            'price' => $service->price,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('appointments.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'vet_id', 'date', 'time', 'type']);
    }

    public function test_store_validates_type_enum()
    {
        $response = $this->post(route('appointments.store'), [
            'pet_id' => 1,
            'vet_id' => 1,
            'date' => today()->addDays(3)->format('Y-m-d'),
            'time' => '10:00',
            'type' => 'invalid_type',
        ]);
        $response->assertSessionHasErrors('type');
    }

    public function test_show()
    {
        $appointment = Appointment::factory()->create();
        $response = $this->get(route('appointments.show', $appointment));
        $response->assertOk();
    }

    public function test_edit()
    {
        $appointment = Appointment::factory()->create();
        $response = $this->get(route('appointments.edit', $appointment));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);
        $newDate = today()->addDays(5)->format('Y-m-d');

        $response = $this->put(route('appointments.update', $appointment), [
            'pet_id' => $appointment->pet_id,
            'vet_id' => $appointment->vet_id,
            'date' => $newDate,
            'time' => '10:00',
            'type' => 'retorno',
            'status' => 'scheduled',
            'reason' => 'Retorno',
        ]);
        $response->assertRedirect(route('appointments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'date' => $newDate,
            'type' => 'retorno',
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $response = $this->delete(route('appointments.destroy', $appointment));
        $response->assertRedirect(route('appointments.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('appointments', ['id' => $appointment->id]);
    }

    public function test_destroy_fails_for_in_progress()
    {
        $appointment = Appointment::factory()->create(['status' => 'in_progress']);

        $response = $this->delete(route('appointments.destroy', $appointment));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
    }

    public function test_destroy_fails_for_completed()
    {
        $appointment = Appointment::factory()->create(['status' => 'completed']);

        $response = $this->delete(route('appointments.destroy', $appointment));
        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('appointments', ['id' => $appointment->id]);
    }

    public function test_update_status()
    {
        $appointment = Appointment::factory()->create(['status' => 'scheduled']);

        $response = $this->patch(route('appointments.update-status', $appointment), [
            'status' => 'in_progress',
        ]);
        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseHas('appointments', [
            'id' => $appointment->id,
            'status' => 'in_progress',
        ]);
    }

    public function test_update_status_validates()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->patch(route('appointments.update-status', $appointment), [
            'status' => 'invalid',
        ]);
        $response->assertSessionHasErrors('status');
    }

    public function test_flow_data()
    {
        Appointment::factory()->create(['status' => 'scheduled', 'date' => today()]);
        $response = $this->get(route('appointments.flow-data'));
        $response->assertOk();
        $response->assertJsonStructure([]);
    }

    public function test_flow_board()
    {
        Appointment::factory()->create(['status' => 'scheduled', 'date' => today()]);
        $response = $this->get(route('appointments.flow-board'));
        $response->assertOk();
    }
}
