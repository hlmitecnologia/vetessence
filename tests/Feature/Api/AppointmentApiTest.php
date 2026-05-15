<?php

namespace Tests\Feature\Api;

use App\Models\Appointment;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AppointmentApiTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('test')->plainTextToken;
        $this->headers = ['Authorization' => 'Bearer ' . $this->token];
    }

    public function test_index_appointments()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->getJson('/api/v1/appointments', $this->headers);
        $response->assertOk();
    }

    public function test_store_appointment()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->postJson('/api/v1/appointments', [
            'pet_id' => $pet->id,
            'vet_id' => $vet->id,
            'date' => now()->addDay()->format('Y-m-d'),
            'time' => '14:00',
            'type' => 'consulta',
            'reason' => 'Check-up',
        ], $this->headers);

        $response->assertCreated();
    }

    public function test_show_appointment()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->getJson("/api/v1/appointments/{$appointment->id}", $this->headers);
        $response->assertOk();
    }

    public function test_update_appointment()
    {
        $appointment = Appointment::factory()->create();

        $response = $this->putJson("/api/v1/appointments/{$appointment->id}", [
            'status' => 'confirmed',
        ], $this->headers);

        $response->assertOk();
    }

    public function test_calendar()
    {
        Appointment::factory()->count(3)->create();

        $response = $this->getJson('/api/v1/appointments/calendar/data?' . http_build_query([
            'start' => now()->subMonth()->format('Y-m-d'),
            'end' => now()->addMonth()->format('Y-m-d'),
        ]), $this->headers);

        $response->assertOk();
    }

    public function test_requires_authentication()
    {
        $response = $this->getJson('/api/v1/appointments');
        $response->assertUnauthorized();
    }
}
