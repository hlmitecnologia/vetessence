<?php

namespace App\Http\Controllers;

use App\Models\Vaccination;
use App\Models\Pet;
use App\Models\User;
use App\Models\Role;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VaccinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaccination::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $vaccinations = $query->orderBy('date', 'desc')->paginate(20);

        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('vaccinations.index', compact('vaccinations', 'pets'));
    }

    public function create(Request $request)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();
        $selectedPet = $request->pet_id ? Pet::find($request->pet_id) : null;

        return view('vaccinations.create', compact('pets', 'veterinarians', 'selectedPet'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vaccine' => 'required|string|max:100',
            'date' => 'required|date',
            'next_date' => 'nullable|date|after:date',
            'batch' => 'nullable|string|max:50',
            'lot' => 'nullable|string|max:50',
            'manufacturer' => 'nullable|string|max:100',
            'vet_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        Vaccination::create($validated);

        return redirect()->route('vaccinations.index')->with('success', 'Vacina registrada!');
    }

    public function show(Vaccination $vaccination)
    {
        $vaccination->load(['pet', 'vet']);
        return view('vaccinations.show', compact('vaccination'));
    }

    public function edit(Vaccination $vaccination)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $veterinarians = $this->getVeterinarians();

        return view('vaccinations.edit', compact('vaccination', 'pets', 'veterinarians'));
    }

    public function update(Request $request, Vaccination $vaccination)
    {
        $validated = $request->validate([
            'vaccine' => 'required|string|max:100',
            'date' => 'required|date',
            'next_date' => 'nullable|date|after:date',
            'batch' => 'nullable|string|max:50',
            'vet_id' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ]);

        $vaccination->update($validated);

        return redirect()->route('vaccinations.index')->with('success', 'Vacina atualizada!');
    }

    public function destroy(Vaccination $vaccination)
    {
        $vaccination->delete();
        return redirect()->route('vaccinations.index')->with('success', 'Vacina excluída!');
    }

    public function certificate(Pet $pet)
    {
        $vaccinations = $pet->vaccinations()->with('vet')->orderBy('date', 'desc')->get();
        $pet->load('tutors');

        $pdf = Pdf::loadView('vaccinations.certificate-pdf', compact('pet', 'vaccinations'));

        return $pdf->download("certificado-vacinas-{$pet->name}.pdf");
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
