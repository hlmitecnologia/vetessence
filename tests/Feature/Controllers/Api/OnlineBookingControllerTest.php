<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Appointment;
use Tests\ModuleTestCase;

class OnlineBookingControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_store_creates_booking()
    {
        $response = $this->postJson('/api/v1/online-bookings', [
            'tutor_name' => 'John Doe',
            'tutor_email' => 'john@example.com',
            'tutor_phone' => '11999999999',
            'pet_name' => 'Rex',
            'pet_species' => 'canine',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
        ]);

        $response->assertCreated()
            ->assertJson(['success' => true])
            ->assertJsonStructure(['booking_id']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->postJson('/api/v1/online-bookings', []);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['tutor_name', 'tutor_email', 'tutor_phone', 'pet_name', 'pet_species', 'preferred_date']);
    }

    public function test_store_validates_preferred_date_must_be_future()
    {
        $response = $this->postJson('/api/v1/online-bookings', [
            'tutor_name' => 'John Doe',
            'tutor_email' => 'john@example.com',
            'tutor_phone' => '11999999999',
            'pet_name' => 'Rex',
            'pet_species' => 'canine',
            'preferred_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['preferred_date']);
    }

    public function test_availability_returns_slots()
    {
        Appointment::factory()->create(['date' => now()->addDays(3)]);

        $response = $this->getJson('/api/v1/online-bookings/availability?date=' . now()->addDays(3)->format('Y-m-d'));

        $response->assertOk()
            ->assertJsonStructure(['date', 'available_slots', 'total_slots'])
            ->assertJson(['date' => now()->addDays(3)->format('Y-m-d')]);
    }

    public function test_availability_validates_date()
    {
        $response = $this->getJson('/api/v1/online-bookings/availability?date=invalid');

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['date']);
    }
}
