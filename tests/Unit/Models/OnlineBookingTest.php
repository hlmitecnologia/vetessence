<?php

namespace Tests\Unit\Models;

use App\Models\OnlineBooking;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OnlineBookingTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $user = User::factory()->create();

        OnlineBooking::create([
            'tutor_name' => 'Maria',
            'tutor_email' => 'maria@email.com',
            'tutor_phone' => '11999999999',
            'pet_name' => 'Rex',
            'pet_species' => 'dog',
            'pet_breed' => 'SRD',
            'preferred_date' => now()->addDay(),
            'preferred_time' => '14:00',
            'reason' => 'consulta',
            'notes' => 'sem alergias',
            'status' => 'pending',
            'converted_appointment_id' => null,
            'staff_notes' => null,
            'handled_by' => $user->id,
            'handled_at' => now(),
            'branch_id' => null,
        ]);

        $this->assertDatabaseHas('online_bookings', [
            'tutor_name' => 'Maria',
            'pet_name' => 'Rex',
            'status' => 'pending',
        ]);
    }

    public function test_pending_scope()
    {
        OnlineBooking::create(['tutor_name' => 'A', 'tutor_email' => 'a@a.com', 'status' => 'pending', 'preferred_date' => now()->addDay()]);
        OnlineBooking::create(['tutor_name' => 'B', 'tutor_email' => 'b@b.com', 'status' => 'confirmed', 'preferred_date' => now()->addDay()]);
        OnlineBooking::create(['tutor_name' => 'C', 'tutor_email' => 'c@c.com', 'status' => 'pending', 'preferred_date' => now()->addDay()]);

        $this->assertCount(2, OnlineBooking::pending()->get());
    }

    public function test_by_status_scope()
    {
        OnlineBooking::create(['tutor_name' => 'A', 'tutor_email' => 'a@a.com', 'status' => 'pending', 'preferred_date' => now()->addDay()]);
        OnlineBooking::create(['tutor_name' => 'B', 'tutor_email' => 'b@b.com', 'status' => 'confirmed', 'preferred_date' => now()->addDay()]);
        OnlineBooking::create(['tutor_name' => 'C', 'tutor_email' => 'c@c.com', 'status' => 'canceled', 'preferred_date' => now()->addDay()]);

        $this->assertCount(1, OnlineBooking::byStatus('confirmed')->get());
    }

    public function test_converted_appointment_relationship()
    {
        $booking = OnlineBooking::create([
            'tutor_name' => 'Test',
            'tutor_email' => 'test@test.com',
            'status' => 'pending',
            'preferred_date' => now()->addDay(),
            'converted_appointment_id' => null,
        ]);

        $this->assertNull($booking->convertedAppointment);
    }
}
