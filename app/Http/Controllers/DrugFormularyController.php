<?php

namespace App\Http\Controllers;

use App\Models\DrugFormulary;
use Illuminate\Http\Request;

class DrugFormularyController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:drug-formulary.view')->only(['index', 'show', 'calculate']);
        $this->middleware('can:drug-formulary.create')->only(['create', 'store']);
        $this->middleware('can:drug-formulary.edit')->only(['edit', 'update']);
        $this->middleware('can:drug-formulary.delete')->only(['destroy']);
    }

    public function index()
    {
        $formularies = DrugFormulary::orderBy('drug')->paginate(20);
        return view('drug-formulary.index', compact('formularies'));
    }

    public function create()
    {
        return redirect()->route('drug-formulary.index');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'drug' => 'required|string|max:150',
            'species' => 'required|string|max:50',
            'dosage_mg_kg' => 'required|numeric|min:0.01',
            'max_dose' => 'nullable|numeric|min:0',
            'route' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        DrugFormulary::create($data);
        return redirect()->route('drug-formulary.index')->with('success', 'Fármaco cadastrado.');
    }

    public function edit($drugFormulary)
    {
        return redirect()->route('drug-formulary.index');
    }

    public function update(Request $request, DrugFormulary $drugFormulary)
    {
        $data = $request->validate([
            'drug' => 'required|string|max:150',
            'species' => 'required|string|max:50',
            'dosage_mg_kg' => 'required|numeric|min:0.01',
            'max_dose' => 'nullable|numeric|min:0',
            'route' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $drugFormulary->update($data);
        return redirect()->route('drug-formulary.index')->with('success', 'Fármaco atualizado.');
    }

    public function show(DrugFormulary $drugFormulary)
    {
        return redirect()->route('drug-formulary.index');
    }

    public function destroy(DrugFormulary $drugFormulary)
    {
        $drugFormulary->delete();
        return redirect()->route('drug-formulary.index')->with('success', 'Fármaco excluído.');
    }

    public function calculate(Request $request)
    {
        $data = $request->validate([
            'drug_formulary_id' => 'required|exists:drug_formulary,id',
            'weight_kg' => 'required|numeric|min:0.01',
            'species' => 'required|string|max:50',
        ]);

        $result = DrugFormulary::calculateDose($data['drug_formulary_id'], $data['weight_kg'], $data['species']);

        if (!$result) {
            return response()->json(['error' => 'Nenhuma dosagem encontrada para este fármaco/espécie.'], 404);
        }

        return response()->json($result);
    }
}
