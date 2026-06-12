<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;

class HospitalizationController extends Controller
{
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
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $tutors = Tutor::orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->orderBy('name')->get();
        return view('hospitalizations.create', compact('pets', 'tutors', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'required|exists:tutors,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'admission_date' => 'required|date',
            'admission_time' => 'nullable',
            'admission_reason' => 'required|string',
            'initial_diagnosis' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'bed' => 'nullable|string|max:50',
            'is_emergency' => 'boolean',
            'status' => 'required|string|max:50',
        ]);

        $validated['is_emergency'] = $request->boolean('is_emergency');

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
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $tutors = Tutor::orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->orderBy('name')->get();
        return view('hospitalizations.edit', compact('hospitalization', 'pets', 'tutors', 'veterinarians'));
    }

    public function update(Request $request, Hospitalization $hospitalization)
    {
        $validated = $request->validate([
            'pet_id' => 'sometimes|required|exists:pets,id',
            'tutor_id' => 'sometimes|required|exists:tutors,id',
            'vet_id' => 'sometimes|required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'admission_date' => 'sometimes|required|date',
            'admission_time' => 'nullable',
            'admission_reason' => 'sometimes|required|string',
            'initial_diagnosis' => 'nullable|string',
            'department' => 'nullable|string|max:100',
            'bed' => 'nullable|string|max:50',
            'is_emergency' => 'boolean',
            'status' => 'sometimes|required|string|max:50',
            'discharged_at' => 'nullable|date',
            'discharge_summary' => 'nullable|string',
            'discharge_instructions' => 'nullable|string',
        ]);

        $validated['is_emergency'] = $request->boolean('is_emergency');

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
