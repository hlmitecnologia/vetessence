<?php

namespace Tests\Unit\Models;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\Pet;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentServiceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => now(),
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
        $service = Service::create(['name' => 'Consulta', 'price' => 100.00, 'is_active' => true]);
        AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);

        $this->assertDatabaseHas('appointment_services', [
            'appointment_id' => $appointment->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);
    }

    public function test_appointment_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => now(),
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
        $service = Service::create(['name' => 'Consulta', 'price' => 100.00, 'is_active' => true]);
        $appointmentService = AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $this->assertInstanceOf(Appointment::class, $appointmentService->appointment);
    }

    public function test_service_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => now(),
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
        $service = Service::create([
            'name' => 'Consultation',
            'price' => 100.00,
            'is_active' => true,
        ]);
        $appointmentService = AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price' => 100.00,
        ]);

        $this->assertInstanceOf(Service::class, $appointmentService->service);
    }

    public function test_subtotal_accessor()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now(),
            'time' => now(),
            'type' => 'consultation',
            'status' => 'scheduled',
        ]);
        $service = Service::create(['name' => 'Consulta', 'price' => 100.00, 'is_active' => true]);
        $appointmentService = AppointmentService::create([
            'appointment_id' => $appointment->id,
            'service_id' => $service->id,
            'quantity' => 2,
            'price' => 100.00,
        ]);

        $this->assertEquals(200.00, $appointmentService->subtotal);
    }
}
