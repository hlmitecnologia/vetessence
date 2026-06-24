<?php

namespace App\Http\Controllers;

use App\Models\ParasiteControl;
use App\Models\Pet;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class ParasiteControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:parasitario');
    }
    public function index(Request $request)
    {
        $query = ParasiteControl::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }
        if ($request->type) {
            $query->where('type', $request->type);
        }
        if ($request->search) {
            $query->whereHas('pet', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $controls = $query->orderBy('application_date', 'desc')->get();
        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('parasite-controls.index', compact('controls', 'pets'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        return view('parasite-controls.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'product_name' => 'required|string|max:200',
            'active_ingredient' => 'nullable|string|max:200',
            'type' => 'required|string|max:50',
            'application_date' => 'required|date',
            'next_due_date' => 'nullable|date|after_or_equal:application_date',
            'dose' => 'nullable|string|max:100',
            'batch' => 'nullable|string|max:100',
            'vet_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $validated['medical_record_id'] = $request->medical_record_id;

        ParasiteControl::create($validated);

        return redirect()->route('parasite-controls.index')
            ->with('success', 'Controle parasitário registrado!');
    }

    public function show(ParasiteControl $parasiteControl)
    {
        $parasiteControl->load(['pet', 'vet']);
        return view('parasite-controls.show', compact('parasiteControl'));
    }

    public function edit(ParasiteControl $parasiteControl)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        return view('parasite-controls.edit', compact('parasiteControl', 'pets', 'veterinarians'));
    }

    public function update(Request $request, ParasiteControl $parasiteControl)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'product_name' => 'required|string|max:200',
            'active_ingredient' => 'nullable|string|max:200',
            'type' => 'required|string|max:50',
            'application_date' => 'required|date',
            'next_due_date' => 'nullable|date|after_or_equal:application_date',
            'dose' => 'nullable|string|max:100',
            'batch' => 'nullable|string|max:100',
            'vet_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $parasiteControl->update($validated);

        return redirect()->route('parasite-controls.index')
            ->with('success', 'Controle parasitário atualizado!');
    }

    public function destroy(ParasiteControl $parasiteControl)
    {
        $parasiteControl->delete();
        return redirect()->route('parasite-controls.index')
            ->with('success', 'Controle parasitário excluído!');
    }

    protected function getVeterinarians()
    {
        return User::where('is_active', true)
            ->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))
            ->orderBy('name')
            ->get();
    }
}
