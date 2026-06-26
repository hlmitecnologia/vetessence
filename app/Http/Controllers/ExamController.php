<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Pet;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:exames');
    }

    public function index(Request $request)
    {
        $query = Exam::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $exams = $query->orderBy('requested_date', 'desc')->get();

        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('exams.index', compact('exams', 'pets'));
    }

    public function create(Request $request)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        $selectedPet = $request->pet_id ? Pet::find($request->pet_id) : null;

        return view('exams.create', compact('pets', 'veterinarians', 'selectedPet'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|string|max:100',
            'requested_date' => 'required|date',
            'vet_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        Exam::create($validated);

        return redirect()->route('exams.index')->with('success', 'Exame solicitado!');
    }

    public function show(Exam $exam)
    {
        $exam->load(['pet', 'vet']);
        return view('exams.show', compact('exam'));
    }

    public function edit(Exam $exam)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();

        return view('exams.edit', compact('exam', 'pets', 'veterinarians'));
    }

    public function update(Request $request, Exam $exam)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:100',
            'status' => 'required|in:requested,collected,analyzing,ready,delivered,cancelled',
            'result_date' => 'nullable|date',
            'result' => 'nullable|string',
            'lab_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $exam->update($validated);

        return redirect()->route('exams.show', $exam)->with('success', 'Exame atualizado!');
    }

    public function destroy(Exam $exam)
    {
        $exam->delete();
        return redirect()->route('exams.index')->with('success', 'Exame excluído!');
    }

    protected function getVeterinarians()
    {
        return User::where('is_active', true)
            ->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))
            ->orderBy('name')
            ->get();
    }
}
