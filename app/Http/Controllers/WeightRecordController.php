<?php

namespace App\Http\Controllers;

use App\Models\WeightRecord;
use App\Models\Pet;
use Illuminate\Http\Request;

class WeightRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = WeightRecord::with(['pet', 'measuredBy']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $weightRecords = $query->orderBy('measurement_date', 'desc')->paginate(20);

        return view('weight-records.index', compact('weightRecords'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('weight-records.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'weight' => 'required|numeric|min:0',
            'bcs' => 'nullable|numeric|min:1|max:9',
            'measurement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $validated['measured_by'] = auth()->id();

        WeightRecord::create($validated);

        return redirect()->route('weight-records.index')->with('success', 'Peso registrado com sucesso!');
    }

    public function show(WeightRecord $weightRecord)
    {
        $weightRecord->load(['pet', 'measuredBy']);
        return view('weight-records.show', compact('weightRecord'));
    }

    public function edit(WeightRecord $weightRecord)
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('weight-records.edit', compact('weightRecord', 'pets'));
    }

    public function update(Request $request, WeightRecord $weightRecord)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'weight' => 'required|numeric|min:0',
            'bcs' => 'nullable|numeric|min:1|max:9',
            'measurement_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $weightRecord->update($validated);

        return redirect()->route('weight-records.index')->with('success', 'Peso atualizado com sucesso!');
    }

    public function destroy(WeightRecord $weightRecord)
    {
        $weightRecord->delete();

        return redirect()->route('weight-records.index')->with('success', 'Registro de peso excluído!');
    }

    public function chartData(Request $request, Pet $pet)
    {
        $records = WeightRecord::where('pet_id', $pet->id)
            ->orderBy('measurement_date', 'asc')
            ->get(['measurement_date', 'weight', 'bcs']);

        return response()->json($records);
    }
}
