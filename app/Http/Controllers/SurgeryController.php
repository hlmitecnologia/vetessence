<?php

namespace App\Http\Controllers;

use App\Models\Surgery;
use App\Models\Pet;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class SurgeryController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:cirurgias');
    }

    public function index(Request $request)
    {
        $query = Surgery::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $surgeries = $query->orderBy('scheduled_date', 'desc')->paginate(20);

        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('surgeries.index', compact('surgeries', 'pets'));
    }

    public function create(Request $request)
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();

        return view('surgeries.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'scheduled_date' => 'required|date',
            'surgery_type' => 'required|string|max:100',
            'pre_op_diagnosis' => 'nullable|string',
            'anesthesia_type' => 'nullable|string|max:100',
            'protocol' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['diagnosis'] = $validated['pre_op_diagnosis'];
        $validated['surgery_notes'] = $validated['notes'];
        unset($validated['pre_op_diagnosis'], $validated['notes']);

        Surgery::create($validated);

        return redirect()->route('surgeries.index')->with('success', 'Cirurgia agendada!');
    }

    public function show(Surgery $surgery)
    {
        $surgery->load(['pet', 'vet']);
        return view('surgeries.show', compact('surgery'));
    }

    public function edit(Surgery $surgery)
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();

        return view('surgeries.edit', compact('surgery', 'pets', 'veterinarians'));
    }

    public function update(Request $request, Surgery $surgery)
    {
        $validated = $request->validate([
            'scheduled_date' => 'required|date',
            'surgery_type' => 'required|string|max:100',
            'status' => 'required|in:scheduled,pre_op,in_progress,post_op,completed,cancelled',
            'anesthesia_type' => 'nullable|string|max:100',
            'protocol' => 'nullable|string',
            'pre_op_diagnosis' => 'nullable|string',
            'post_op_notes' => 'nullable|string',
            'surgery_duration' => 'nullable|integer',
            'notes' => 'nullable|string',
        ]);

        $validated['diagnosis'] = $validated['pre_op_diagnosis'] ?? $surgery->diagnosis;
        $validated['surgery_notes'] = $validated['notes'] ?? $surgery->surgery_notes;
        unset($validated['pre_op_diagnosis'], $validated['notes']);

        $surgery->update($validated);

        return redirect()->route('surgeries.show', $surgery)->with('success', 'Cirurgia atualizada!');
    }

    public function destroy(Surgery $surgery)
    {
        if ($surgery->status === 'in_progress') {
            return back()->with('error', 'Não é possível excluir cirurgia em andamento.');
        }

        $surgery->delete();
        return redirect()->route('surgeries.index')->with('success', 'Cirurgia excluída!');
    }

    protected function getVeterinarians()
    {
        return User::where('is_active', true)
            ->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))
            ->orderBy('name')
            ->get();
    }
}
