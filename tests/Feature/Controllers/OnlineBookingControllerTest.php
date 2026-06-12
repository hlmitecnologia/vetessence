<?php

namespace Tests\Feature\Controllers;

use App\Models\OnlineBooking;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class OnlineBookingControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        OnlineBooking::factory()->count(3)->create();
        $response = $this->get(route('online-bookings.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        OnlineBooking::factory()->create(['status' => 'pending']);
        OnlineBooking::factory()->create(['status' => 'confirmed']);
        $response = $this->get(route('online-bookings.index', ['status' => 'pending']));
        $response->assertOk();
    }

    public function test_show()
    {
        $booking = OnlineBooking::factory()->create();
        $response = $this->get(route('online-bookings.show', $booking));
        $response->assertOk();
    }

    public function test_confirm_creates_appointment()
    {
        $vet = User::factory()->create();
        $booking = OnlineBooking::factory()->create(['status' => 'pending']);

        $response = $this->post(route('online-bookings.confirm', $booking), [
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'user_id' => $vet->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('online_bookings', [
            'id' => $booking->id,
            'status' => 'confirmed',
        ]);
    }

    public function test_confirm_fails_when_already_processed()
    {
        $booking = OnlineBooking::factory()->create(['status' => 'confirmed']);
        $response = $this->post(route('online-bookings.confirm', $booking), [
            'appointment_date' => now()->addDay()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'user_id' => User::factory()->create()->id,
        ]);
        $response->assertSessionHas('error');
    }

    public function test_reject_updates_status()
    {
        $booking = OnlineBooking::factory()->create(['status' => 'pending']);
        $response = $this->post(route('online-bookings.reject', $booking), [
            'reason' => 'Horário indisponível',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('online_bookings', [
            'id' => $booking->id,
            'status' => 'rejected',
        ]);
    }

    public function test_reject_fails_when_already_processed()
    {
        $booking = OnlineBooking::factory()->create(['status' => 'confirmed']);
        $response = $this->post(route('online-bookings.reject', $booking));
        $response->assertSessionHas('error');
    }

    public function test_destroy_deletes_booking()
    {
        $booking = OnlineBooking::factory()->create();
        $response = $this->delete(route('online-bookings.destroy', $booking));
        $response->assertRedirect();
        $this->assertDatabaseMissing('online_bookings', ['id' => $booking->id]);
    }
}
