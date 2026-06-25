<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use App\Services\VetAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        $petIds = $tutor->pets()->pluck('pets.id');

        $upcoming = Appointment::with(['pet', 'vet', 'branch'])
            ->whereIn('pet_id', $petIds)
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $past = Appointment::with(['pet', 'vet', 'branch'])
            ->whereIn('pet_id', $petIds)
            ->where('date', '<', today())
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(20)
            ->get();

        return view('portal.appointments.index', compact('upcoming', 'past'));
    }

    public function show($id)
    {
        $tutor = Auth::guard('tutor')->user();
        $petIds = $tutor->pets()->pluck('pets.id');

        $appointment = Appointment::with(['pet', 'vet', 'branch'])
            ->whereIn('pet_id', $petIds)
            ->findOrFail($id);

        return view('portal.appointments.show', compact('appointment'));
    }

    public function create()
    {
        $tutor = Auth::guard('tutor')->user();
        $pets = $tutor->pets;
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        $vets = User::where('is_active', true)
            ->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))
            ->orderBy('name')
            ->get();

        return view('portal.appointments.create', compact('pets', 'vets', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
            'reason' => 'nullable|string|max:500',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $tutor = Auth::guard('tutor')->user();

        $pet = $tutor->pets()->where('pets.id', $validated['pet_id'])->first();
        if (!$pet) {
            return back()->with('error', 'Pet não encontrado.');
        }

        $availability = app(VetAvailabilityService::class);
        if (!$availability->isSlotAvailable($validated['vet_id'], $validated['date'], $validated['time'])) {
            return back()->with('error', 'Horário indisponível. Por favor, escolha outro horário.');
        }

        DB::beginTransaction();
        try {
            $appointment = Appointment::create([
                'pet_id' => $validated['pet_id'],
                'vet_id' => $validated['vet_id'],
                'date' => $validated['date'],
                'time' => $validated['time'],
                'type' => 'consulta',
                'reason' => $validated['reason'] ?? 'Agendamento pelo portal',
                'status' => 'scheduled',
                'branch_id' => $validated['branch_id'],
            ]);

            DB::commit();

            return redirect()->route('portal.appointments.index')
                ->with('success', 'Consulta agendada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao agendar consulta. Tente novamente.');
        }
    }
}
