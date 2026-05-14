<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\PetDeathRecord;
use Illuminate\Http\Request;

class PetDeathRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:obito');
    }

    public function index()
    {
        $records = PetDeathRecord::with(['pet', 'registeredBy'])->latest()->paginate(20);
        return view('pet-death-records.index', compact('records'));
    }

    public function create()
    {
        $pets = Pet::orderBy('name')->get();
        return view('pet-death-records.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'death_date' => 'required|date',
            'cause' => 'nullable|string|max:255',
            'attending_vet' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'disposition' => 'nullable|string|max:50',
        ]);

        $data['registered_by'] = auth()->id();

        PetDeathRecord::create($data);

        return redirect()->route('pet-death-records.index')
            ->with('success', 'Registro de óbito criado.');
    }

    public function show(PetDeathRecord $petDeathRecord)
    {
        $petDeathRecord->load(['pet', 'registeredBy']);
        return view('pet-death-records.show', compact('petDeathRecord'));
    }

    public function edit(PetDeathRecord $petDeathRecord)
    {
        $pets = Pet::orderBy('name')->get();
        return view('pet-death-records.edit', compact('petDeathRecord', 'pets'));
    }

    public function update(Request $request, PetDeathRecord $petDeathRecord)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'death_date' => 'required|date',
            'cause' => 'nullable|string|max:255',
            'attending_vet' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'disposition' => 'nullable|string|max:50',
        ]);

        $petDeathRecord->update($data);

        return redirect()->route('pet-death-records.index')
            ->with('success', 'Registro de óbito atualizado.');
    }

    public function destroy(PetDeathRecord $petDeathRecord)
    {
        $petDeathRecord->delete();
        return redirect()->route('pet-death-records.index')
            ->with('success', 'Registro de óbito removido.');
    }
}
