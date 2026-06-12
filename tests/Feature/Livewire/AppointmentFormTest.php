<?php

namespace Tests\Feature\Livewire;

use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class AppointmentFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_can_create_appointment_with_required_fields()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create(['is_active' => true]);

        Livewire::test('appointment-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) $vet->id)
            ->set('date', now()->addDay()->format('Y-m-d'))
            ->set('time', '10:00')
            ->set('type', 'consulta')
            ->call('save');

        $this->assertDatabaseHas('appointments', [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'type' => 'consulta',
            'status' => 'scheduled',
        ]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('appointment-form')
            ->set('pet_id', '')
            ->set('vet_id', '')
            ->set('date', '')
            ->set('time', '')
            ->set('type', '')
            ->call('save')
            ->assertHasErrors(['pet_id', 'vet_id', 'date', 'time', 'type']);
    }

    public function test_can_create_with_services()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create(['is_active' => true]);
        $service = Service::factory()->create(['price' => 150.00, 'is_active' => true]);

        Livewire::test('appointment-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) $vet->id)
            ->set('date', now()->addDay()->format('Y-m-d'))
            ->set('time', '14:30')
            ->set('type', 'consulta')
            ->set('selectedServices', [$service->id])
            ->call('save');

        $appointment = \App\Models\Appointment::where('pet_id', $pet->id)->first();
        $this->assertNotNull($appointment);
        $this->assertDatabaseHas('appointment_services', [
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'price' => 150.00,
        ]);
    }

    public function test_validates_invalid_type()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create(['is_active' => true]);

        Livewire::test('appointment-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) $vet->id)
            ->set('date', now()->addDay()->format('Y-m-d'))
            ->set('time', '10:00')
            ->set('type', 'invalid_type')
            ->call('save')
            ->assertHasErrors(['type']);
    }

    public function test_can_select_different_appointment_types()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create(['is_active' => true]);

        foreach (['consulta', 'retorno', 'emergencia', 'cirurgia', 'vacina', 'exame'] as $type) {
            Livewire::test('appointment-form')
                ->set('pet_id', (string) $pet->id)
                ->set('vet_id', (string) $vet->id)
                ->set('date', now()->addDay()->format('Y-m-d'))
                ->set('time', '10:00')
                ->set('type', $type)
                ->call('save');

            $this->assertDatabaseHas('appointments', [
                'pet_id' => $pet->id,
                'type' => $type,
                'status' => 'scheduled',
            ]);
        }
    }

    public function test_calculates_total_with_services()
    {
        $serviceA = Service::factory()->create(['price' => 100.00, 'is_active' => true]);
        $serviceB = Service::factory()->create(['price' => 200.00, 'is_active' => true]);

        Livewire::test('appointment-form')
            ->set('selectedServices', [$serviceA->id, $serviceB->id])
            ->assertSet('total', 300.00);
    }

    public function test_create_appointment_with_reason()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create(['is_active' => true]);

        Livewire::test('appointment-form')
            ->set('pet_id', (string) $pet->id)
            ->set('vet_id', (string) $vet->id)
            ->set('date', now()->addDay()->format('Y-m-d'))
            ->set('time', '11:00')
            ->set('type', 'consulta')
            ->set('reason', 'Check-up anual')
            ->call('save');

        $this->assertDatabaseHas('appointments', [
            'pet_id' => $pet->id,
            'reason' => 'Check-up anual',
        ]);
    }
}
