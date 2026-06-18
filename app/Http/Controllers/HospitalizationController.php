<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalizationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:hospitalizacao');
    }

    public function index(Request $request)
    {
        $query = Hospitalization::with(['pet', 'tutor', 'vet', 'dailyRecords', 'fluidTherapies', 'prescriptions']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->department) {
            $query->where('department', $request->department);
        }

        if ($request->date_from) {
            $query->whereDate('admission_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('admission_date', '<=', $request->date_to);
        }

        $hospitalizations = $query->orderBy('admission_date', 'desc')->paginate(20);

        return view('hospitalizations.index', compact('hospitalizations'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))->orderBy('name')->get();
        return view('hospitalizations.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'admission_date' => 'required|date',
            'admission_time' => 'nullable',
            'admission_reason' => 'required|string',
            'initial_diagnosis' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'bed' => 'nullable|string|max:50',
            'is_emergency' => 'boolean',
        ]);

        $validated['is_emergency'] = $request->boolean('is_emergency');
        $validated['status'] = 'active';
        $validated['tutor_id'] = Pet::findOrFail($validated['pet_id'])->tutors()->first()?->id;

        Hospitalization::create($validated);

        return redirect()->route('hospitalizations.index')->with('success', 'Internação cadastrada com sucesso!');
    }

    public function show(Hospitalization $hospitalization)
    {
        $hospitalization->load(['pet', 'tutor', 'vet', 'dailyRecords.user', 'fluidTherapies', 'prescriptions']);

        return view('hospitalizations.show', compact('hospitalization'));
    }

    public function edit(Hospitalization $hospitalization)
    {
        $hospitalization->load(['pet', 'tutor', 'vet']);
        return view('hospitalizations.edit', compact('hospitalization'));
    }

    public function update(Request $request, Hospitalization $hospitalization)
    {
        $validated = $request->validate([
            'bed' => 'nullable|string|max:50',
            'status' => 'sometimes|required|string|max:50',
            'discharged_at' => 'nullable|date',
            'discharge_summary' => 'nullable|string',
            'discharge_instructions' => 'nullable|string',
        ]);

        $hospitalization->update($validated);

        return redirect()->route('hospitalizations.index')->with('success', 'Internação atualizada com sucesso!');
    }

    public function destroy(Hospitalization $hospitalization)
    {
        if ($hospitalization->status === 'active' || $hospitalization->status === 'internado') {
            return back()->with('error', 'Não é possível excluir uma internação com status ativo.');
        }

        $hospitalization->delete();

        return redirect()->route('hospitalizations.index')->with('success', 'Internação excluída com sucesso!');
    }
}
