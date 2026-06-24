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
            'is_recurring' => 'nullable|boolean',
            'recurrence_rule' => 'nullable|string|max:100',
            'recurrence_end_date' => 'nullable|date|after:date',
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
            'is_recurring' => $validated['is_recurring'] ?? false,
            'recurrence_rule' => $validated['recurrence_rule'] ?? null,
            'recurrence_end_date' => $validated['recurrence_end_date'] ?? null,
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

    public function calendar(Request $request)
    {
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $query = Appointment::with(['pet.tutors', 'vet', 'services.service'])
            ->whereBetween('date', [$request->start, $request->end]);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        } else {
            $query->withoutGlobalScope(\App\Scopes\BranchScope::class);
        }

        $appointments = $query->orderBy('date')->orderBy('time')->get();

        $statusColors = [
            'scheduled' => '#007bff',
            'confirmed' => '#28a745',
            'in_progress' => '#ffc107',
            'completed' => '#6c757d',
            'cancelled' => '#dc3545',
            'no_show' => '#343a40',
        ];

        $typeLabels = [
            'consulta' => 'Consulta',
            'retorno' => 'Retorno',
            'emergencia' => 'Emergência',
            'cirurgia' => 'Cirurgia',
            'vacina' => 'Vacina',
            'exame' => 'Exame',
        ];

        $events = $appointments->map(function ($a) use ($statusColors, $typeLabels) {
            $start = "{$a->date->format('Y-m-d')}T{$a->time->format('H:i:s')}";
            $end = \Carbon\Carbon::parse($start)->addMinutes($a->duration ?? 30)->format('Y-m-d\TH:i:s');

            return [
                'id' => $a->id,
                'title' => ($a->pet->name ?? '?') . ' - ' . ($typeLabels[$a->type] ?? $a->type),
                'start' => $start,
                'end' => $end,
                'backgroundColor' => $statusColors[$a->status] ?? '#6c757d',
                'borderColor' => $statusColors[$a->status] ?? '#6c757d',
                'textColor' => in_array($a->status, ['in_progress', 'cancelled', 'no_show']) ? '#000' : '#fff',
                'extendedProps' => [
                    'pet_name' => $a->pet->name ?? '',
                    'tutor_name' => $a->pet->tutors->first()->name ?? '',
                    'vet_name' => $a->vet->name ?? '',
                    'type' => $typeLabels[$a->type] ?? $a->type,
                    'status' => $a->status,
                    'reason' => $a->reason,
                    'time' => $a->time->format('H:i'),
                ],
            ];
        });

        return response()->json($events);
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $validated = $request->validate([
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required',
            'status' => 'sometimes|required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'vet_id' => 'sometimes|required|exists:users,id',
            'type' => 'sometimes|required|in:consulta,retorno,emergencia,cirurgia,vacina,exame',
            'reason' => 'nullable|string',
        ]);

        $appointment->update($validated);

        return response()->json($appointment);
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
