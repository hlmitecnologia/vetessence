<?php

namespace App\Http\Controllers;

use App\Models\OnlineBooking;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Http\Request;

class OnlineBookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:agendamento-online');
    }
    public function index(Request $request)
    {
        $query = OnlineBooking::with(['convertedAppointment', 'handledBy']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('tutor_name', 'like', "%{$request->search}%")
                  ->orWhere('pet_name', 'like', "%{$request->search}%")
                  ->orWhere('tutor_email', 'like', "%{$request->search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('online-bookings.index', compact('bookings'));
    }

    public function show(OnlineBooking $onlineBooking)
    {
        $onlineBooking->load(['convertedAppointment', 'handledBy']);
        return view('online-bookings.show', compact('onlineBooking'));
    }

    public function confirm(Request $request, OnlineBooking $onlineBooking)
    {
        if ($onlineBooking->status !== 'pending') {
            return back()->with('error', 'Solicitação já processada.');
        }

        $validated = $request->validate([
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|string|max:20',
            'user_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $tutor = Tutor::firstOrCreate(
            ['email' => $onlineBooking->tutor_email],
            [
                'phone' => $onlineBooking->tutor_phone,
            ]
        );

        $pet = Pet::firstOrCreate(
            ['name' => $onlineBooking->pet_name, 'species' => $onlineBooking->pet_species],
            ['breed' => $onlineBooking->pet_breed ?? '', 'birth_date' => null, 'is_active' => true]
        );

        if (!$pet->tutors()->where('tutor_id', $tutor->id)->exists()) {
            $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        }

        $appointment = Appointment::create([
            'pet_id' => $pet->id,
            'vet_id' => $validated['user_id'],
            'date' => $validated['appointment_date'],
            'time' => $validated['appointment_time'],
            'type' => 'consulta',
            'status' => 'scheduled',
            'notes' => $validated['notes'] ?? $onlineBooking->reason,
        ]);

        $onlineBooking->update([
            'status' => 'confirmed',
            'converted_appointment_id' => $appointment->id,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
            'staff_notes' => $validated['notes'] ?? null,
        ]);

        return redirect()->route('online-bookings.show', $onlineBooking)
            ->with('success', 'Agendamento confirmado e convertido em consulta!');
    }

    public function reject(Request $request, OnlineBooking $onlineBooking)
    {
        if ($onlineBooking->status !== 'pending') {
            return back()->with('error', 'Solicitação já processada.');
        }

        $onlineBooking->update([
            'status' => 'rejected',
            'staff_notes' => $request->reason,
            'handled_by' => auth()->id(),
            'handled_at' => now(),
        ]);

        return redirect()->route('online-bookings.show', $onlineBooking)
            ->with('success', 'Solicitação rejeitada.');
    }

    public function destroy(OnlineBooking $onlineBooking)
    {
        $onlineBooking->delete();
        return redirect()->route('online-bookings.index')->with('success', 'Solicitação excluída.');
    }
}
