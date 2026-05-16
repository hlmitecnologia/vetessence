<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Role;
use App\Models\User;
use App\Events\AppointmentCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Appointment::with(['pet', 'vet']);

        if ($request->date) {
            $query->whereDate('date', $request->date);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->vet_id) {
            $query->where('vet_id', $request->vet_id);
        }

        $appointments = $query->orderBy('date')->orderBy('time')->paginate(20);

        $veterinarians = $this->getVeterinarians();

        return view('appointments.index', compact('appointments', 'veterinarians'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        $services = Service::where('is_active', true)->orderBy('name')->get();

        return view('appointments.create', compact('pets', 'veterinarians', 'services'));
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
            'services.*' => 'exists:services,id',
            'is_recurring' => 'nullable|boolean',
            'recurrence_rule' => 'nullable|string|max:100',
            'recurrence_end_date' => 'nullable|date|after:date',
        ]);

        DB::beginTransaction();
        try {
            $appointment = Appointment::create([
                'pet_id' => $validated['pet_id'],
                'vet_id' => $validated['vet_id'],
                'date' => $validated['date'],
                'time' => $validated['time'],
                'type' => $validated['type'],
                'reason' => $validated['reason'] ?? null,
                'status' => 'scheduled',
                'created_by' => auth()->id(),
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

            DB::commit();
            return redirect()->route('appointments.index')->with('success', 'Consulta agendada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao agendar consulta.')->withInput();
        }
    }

    public function show(Appointment $appointment)
    {
        $appointment->load(['pet.tutors', 'vet', 'services.service']);
        return view('appointments.show', compact('appointment'));
    }

    public function edit(Appointment $appointment)
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $appointment->load('services');

        return view('appointments.edit', compact('appointment', 'pets', 'veterinarians', 'services'));
    }

    public function update(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'time' => 'required',
            'type' => 'required|in:consulta,retorno,emergencia,cirurgia,vacina,exame',
            'status' => 'required|in:scheduled,confirmed,in_progress,completed,cancelled,no_show',
            'reason' => 'nullable|string',
        ]);

        $wasCompleted = $request->status === 'completed' && $appointment->status !== 'completed';
        $appointment->update($validated);

        if ($wasCompleted) {
            event(new AppointmentCompleted($appointment));
        }

        return redirect()->route('appointments.index')->with('success', 'Consulta atualizada com sucesso!');
    }

    public function destroy(Appointment $appointment)
    {
        if (in_array($appointment->status, ['in_progress', 'completed'])) {
            return back()->with('error', 'Não é possível excluir consulta em andamento ou concluída.');
        }

        $appointment->delete();

        return redirect()->route('appointments.index')->with('success', 'Consulta excluída com sucesso!');
    }

    public function flowData()
    {
        $appointments = Appointment::with(['pet.tutors', 'vet'])
            ->whereDate('date', today())
            ->orderBy('time')
            ->get()
            ->groupBy('status');

        return response()->json($appointments);
    }

    public function flowBoard()
    {
        $statuses = ['scheduled', 'checked_in', 'waiting', 'in_progress', 'completed', 'cancelled', 'no_show'];
        $appointments = Appointment::with(['pet', 'vet'])
            ->whereDate('date', today())
            ->orderBy('time')
            ->get()
            ->groupBy('status');

        return view('appointments.flow-board', compact('appointments', 'statuses'));
    }

    public function updateStatus(Request $request, Appointment $appointment)
    {
        $request->validate([
            'status' => 'required|in:scheduled,checked_in,waiting,in_progress,completed,cancelled,no_show',
        ]);

        $wasCompleted = $request->status === 'completed' && $appointment->status !== 'completed';
        $appointment->update(['status' => $request->status]);

        if ($wasCompleted) {
            event(new AppointmentCompleted($appointment));
        }

        return response()->json(['success' => true]);
    }

    public function reschedule(Request $request, Appointment $appointment)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $appointment->update($validated);

        return response()->json(['success' => true]);
    }

    protected function getVeterinarians()
    {
        $vetRole = Role::where('slug', 'veterinario')->first();
        if (!$vetRole) {
            return collect();
        }
        return User::where('role_id', $vetRole->id)->where('is_active', true)->orderBy('name')->get();
    }
}
