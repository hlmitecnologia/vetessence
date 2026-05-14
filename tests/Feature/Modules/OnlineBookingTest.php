<?php

namespace Tests\Feature\Modules;

use App\Models\OnlineBooking;
use App\Models\Appointment;
use Tests\ModuleTestCase;

class OnlineBookingTest extends ModuleTestCase
{
    public function test_public_api_store()
    {
        $response = $this->postJson('/api/v1/online-bookings', [
            'tutor_name' => 'João',
            'tutor_email' => 'joao@test.com',
            'tutor_phone' => '11999999999',
            'pet_name' => 'Rex',
            'pet_species' => 'canine',
            'preferred_date' => now()->addDays(3)->format('Y-m-d'),
            'preferred_time' => '14:00',
            'reason' => 'Consulta de rotina',
        ]);
        $response->assertCreated()->assertJson(['success' => true]);
        $this->assertDatabaseHas('online_bookings', ['tutor_email' => 'joao@test.com']);
    }

    public function test_confirm_creates_appointment()
    {
        $this->loginAs('veterinario');
        $booking = OnlineBooking::factory()->create(['status' => 'pending']);

        $response = $this->post(route('online-bookings.confirm', $booking), [
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '10:00',
            'user_id' => auth()->id(),
        ]);
        $response->assertRedirect();
        $this->assertEquals('confirmed', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->converted_appointment_id);
    }

    public function test_reject_booking()
    {
        $this->loginAs('veterinario');
        $booking = OnlineBooking::factory()->create(['status' => 'pending']);
        $this->post(route('online-bookings.reject', $booking), ['reason' => 'Sem vagas']);
        $this->assertEquals('rejected', $booking->fresh()->status);
    }

    public function test_public_availability()
    {
        $response = $this->getJson('/api/v1/online-bookings/availability?date=' . now()->addDays(1)->format('Y-m-d'));
        $response->assertOk()->assertJsonStructure(['date', 'available_slots', 'total_slots']);
    }
}
