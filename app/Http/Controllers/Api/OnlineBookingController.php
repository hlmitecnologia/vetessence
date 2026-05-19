<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\OnlineBooking;
use Illuminate\Http\Request;

class OnlineBookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_name' => 'required|string|max:255',
            'tutor_email' => 'required|email|max:255',
            'tutor_phone' => 'required|string|max:50',
            'pet_name' => 'required|string|max:255',
            'pet_species' => 'required|string|max:50',
            'pet_breed' => 'nullable|string|max:100',
            'preferred_date' => 'required|date|after_or_equal:today',
            'preferred_time' => 'nullable|string|max:20',
            'reason' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $booking = OnlineBooking::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Solicitação de agendamento recebida com sucesso! Entraremos em contato em breve.',
            'booking_id' => $booking->id,
        ], 201);
    }

    public function availability(Request $request)
    {
        $request->validate(['date' => 'required|date']);

        $date = $request->date;
        $existing = Appointment::whereDate('date', $date)->count();

        $maxSlots = 20;
        $available = max(0, $maxSlots - $existing);

        return response()->json([
            'date' => $date,
            'available_slots' => $available,
            'total_slots' => $maxSlots,
        ]);
    }
}
