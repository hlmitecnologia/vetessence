<?php

namespace App\Http\Controllers;

use App\Models\VaccineProtocol;
use Illuminate\Http\Request;

class VaccineProtocolController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:protocolo-vacinas');
    }
    public function index(Request $request)
    {
        $query = VaccineProtocol::query();

        if ($request->species) {
            $query->where('species', $request->species);
        }

        if ($request->is_core !== null) {
            $query->where('is_core', $request->is_core);
        }

        $protocols = $query->orderBy('species')->orderBy('age_start_weeks')->paginate(20);

        return view('vaccine-protocols.index', compact('protocols'));
    }

    public function create()
    {
        return view('vaccine-protocols.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'species' => 'required|string|max:50',
            'vaccine_name' => 'required|string|max:200',
            'age_start_weeks' => 'nullable|integer|min:0',
            'age_end_weeks' => 'nullable|integer|min:0|gte:age_start_weeks',
            'is_initial' => 'boolean',
            'dose_number' => 'nullable|integer|min:1',
            'booster_interval_months' => 'nullable|integer|min:1',
            'is_core' => 'boolean',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        VaccineProtocol::create($validated);

        return redirect()->route('vaccine-protocols.index')
            ->with('success', 'Protocolo cadastrado com sucesso!');
    }

    public function show(VaccineProtocol $vaccineProtocol)
    {
        return view('vaccine-protocols.show', compact('vaccineProtocol'));
    }

    public function edit(VaccineProtocol $vaccineProtocol)
    {
        return view('vaccine-protocols.edit', compact('vaccineProtocol'));
    }

    public function update(Request $request, VaccineProtocol $vaccineProtocol)
    {
        $validated = $request->validate([
            'species' => 'required|string|max:50',
            'vaccine_name' => 'required|string|max:200',
            'age_start_weeks' => 'nullable|integer|min:0',
            'age_end_weeks' => 'nullable|integer|min:0|gte:age_start_weeks',
            'is_initial' => 'boolean',
            'dose_number' => 'nullable|integer|min:1',
            'booster_interval_months' => 'nullable|integer|min:1',
            'is_core' => 'boolean',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $vaccineProtocol->update($validated);

        return redirect()->route('vaccine-protocols.index')
            ->with('success', 'Protocolo atualizado com sucesso!');
    }

    public function destroy(VaccineProtocol $vaccineProtocol)
    {
        $vaccineProtocol->delete();

        return redirect()->route('vaccine-protocols.index')
            ->with('success', 'Protocolo excluído com sucesso!');
    }
}
