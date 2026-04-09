<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Service;
use App\Models\AppointmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $appointments = $query->orderBy('date', 'desc')->orderBy('time', 'desc')->paginate(20);

        return response()->json($appointments);
    }

    public function show($id)
    {
        $appointment = Appointment::with(['pet.tutors', 'vet', 'services.service'])->findOrFail($id);

        return response()->json([
            'id' => $appointment->id,
            'pet' => [
                'id' => $appointment->pet->id,
                'name' => $appointment->pet->name,
                'species' => $appointment->pet->species,
            ],
            'vet' => [
                'id' => $appointment->vet->id,
                'name' => $appointment->vet->name,
            ],
            'date' => $appointment->date,
            'time' => $appointment->time,
            'type' => $appointment->type,
            'status' => $appointment->status,
            'reason' => $appointment->reason,
            'services' => $appointment->services->map(function ($s) {
                return [
                    'name' => $s->service->name,
                    'price' => $s->price,
                ];
            }),
            'total' => $appointment->total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required',
            'type' => 'required|in:consulta,retorno,emergencia,cirurgia,vacina,exame',
            'reason' => 'nullable|string',
            'services' => 'nullable|array',
        ]);

        $appointment = Appointment::create([
            'pet_id' => $validated['pet_id'],
            'vet_id' => $validated['vet_id'],
            'date' => $validated['date'],
            'time' => $validated['time'],
            'type' => $validated['type'],
            'reason' => $validated['reason'] ?? null,
            'status' => 'scheduled',
            'created_by' => $request->user()->id,
        ]);

        if (!empty($validated['services'])) {
            foreach ($validated['services'] as $serviceId) {
                $service = Service::find($serviceId);
                AppointmentService::create([
                    'appointment_id' => $appointment->id,
                    'service_id' => $serviceId,
                    'price' => $service->price,
                    'quantity' => 1,
                ]);
            }
        }

        return response()->json($appointment, 201);
    }

    public function myAppointments(Request $request)
    {
        $tutor = $request->user()->tutor;
        $tutorId = $tutor ? $tutor->id : null;

        if (!$tutorId) {
            return response()->json(['message' => 'Usuário não é tutor.'], 403);
        }

        $appointments = Appointment::whereHas('pet.tutors', function ($q) use ($tutorId) {
                $q->where('tutors.id', $tutorId);
            })
            ->with(['pet', 'vet'])
            ->orderBy('date', 'desc')
            ->paginate(20);

        return response()->json($appointments);
    }
}
