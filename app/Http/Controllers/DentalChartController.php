<?php

namespace App\Http\Controllers;

use App\Models\DentalChart;
use App\Models\DentalCondition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DentalChartController extends Controller
{
    public function index(Request $request)
    {
        $query = DentalChart::with(['pet', 'vet', 'conditions']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        if ($request->search) {
            $query->whereHas('pet', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        $charts = $query->orderBy('examination_date', 'desc')->paginate(20);

        return view('dental-charts.index', compact('charts'));
    }

    public function create()
    {
        return view('dental-charts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'examination_date' => 'required|date',
            'procedure_type' => 'nullable|string|max:100',
            'tartar_index' => 'nullable|string|max:50',
            'gingivitis_index' => 'nullable|string|max:50',
            'halitosis' => 'nullable|string|max:50',
            'general_notes' => 'nullable|string',
            'conditions' => 'nullable|array',
            'conditions.*.tooth_number' => 'nullable|string|max:20',
            'conditions.*.quadrant' => 'nullable|string|max:10',
            'conditions.*.condition' => 'required_with:conditions|string|max:100',
            'conditions.*.severity' => 'nullable|string|max:50',
            'conditions.*.notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $conditions = $validated['conditions'] ?? [];
            unset($validated['conditions']);

            $chart = DentalChart::create($validated);

            foreach ($conditions as $condData) {
                $condData['dental_chart_id'] = $chart->id;
                DentalCondition::create($condData);
            }

            DB::commit();
            return redirect()->route('dental-charts.index')->with('success', 'Odontograma cadastrado com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar odontograma.')->withInput();
        }
    }

    public function show(DentalChart $dentalChart)
    {
        $dentalChart->load(['pet', 'vet', 'appointment', 'conditions']);
        return view('dental-charts.show', compact('dentalChart'));
    }

    public function edit(DentalChart $dentalChart)
    {
        $dentalChart->load('conditions');
        return view('dental-charts.edit', compact('dentalChart'));
    }

    public function update(Request $request, DentalChart $dentalChart)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'examination_date' => 'required|date',
            'procedure_type' => 'nullable|string|max:100',
            'tartar_index' => 'nullable|string|max:50',
            'gingivitis_index' => 'nullable|string|max:50',
            'halitosis' => 'nullable|string|max:50',
            'general_notes' => 'nullable|string',
            'conditions' => 'nullable|array',
            'conditions.*.tooth_number' => 'nullable|string|max:20',
            'conditions.*.quadrant' => 'nullable|string|max:10',
            'conditions.*.condition' => 'required_with:conditions|string|max:100',
            'conditions.*.severity' => 'nullable|string|max:50',
            'conditions.*.notes' => 'nullable|string',
            'conditions.*.id' => 'nullable|exists:dental_conditions,id',
        ]);

        DB::beginTransaction();
        try {
            $conditions = $validated['conditions'] ?? [];
            unset($validated['conditions']);

            $dentalChart->update($validated);

            $existingIds = [];
            foreach ($conditions as $condData) {
                if (isset($condData['id'])) {
                    DentalCondition::where('id', $condData['id'])
                        ->where('dental_chart_id', $dentalChart->id)
                        ->update($condData);
                    $existingIds[] = $condData['id'];
                } else {
                    $condData['dental_chart_id'] = $dentalChart->id;
                    $condition = DentalCondition::create($condData);
                    $existingIds[] = $condition->id;
                }
            }

            DentalCondition::where('dental_chart_id', $dentalChart->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            DB::commit();
            return redirect()->route('dental-charts.index')->with('success', 'Odontograma atualizado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar odontograma.')->withInput();
        }
    }

    public function destroy(DentalChart $dentalChart)
    {
        $dentalChart->conditions()->delete();
        $dentalChart->delete();

        return redirect()->route('dental-charts.index')->with('success', 'Odontograma excluído!');
    }
}
