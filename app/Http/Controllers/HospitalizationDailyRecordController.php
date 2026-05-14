<?php

namespace App\Http\Controllers;

use App\Models\HospitalizationDailyRecord;
use App\Models\Hospitalization;
use Illuminate\Http\Request;

class HospitalizationDailyRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = HospitalizationDailyRecord::with(['hospitalization.pet', 'user']);

        if ($request->hospitalization_id) {
            $query->where('hospitalization_id', $request->hospitalization_id);
        }

        $records = $query->orderBy('record_date', 'desc')->paginate(20);

        return view('hospitalization-daily-records.index', compact('records'));
    }

    public function create()
    {
        $hospitalizations = Hospitalization::with('pet')->where('status', 'active')->get();
        return view('hospitalization-daily-records.create', compact('hospitalizations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'hospitalization_id' => 'required|exists:hospitalizations,id',
            'record_date' => 'required|date',
            'shift' => 'required|string|max:50',
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'appetite' => 'nullable|string|max:50',
            'hydration' => 'nullable|string|max:50',
            'urination' => 'nullable|string|max:50',
            'defecation' => 'nullable|string|max:50',
            'medications_given' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $validated['user_id'] = auth()->id();

        HospitalizationDailyRecord::create($validated);

        return redirect()->route('hospitalization-daily-records.index')
            ->with('success', ' Registro diário cadastrado com sucesso!');
    }

    public function show(HospitalizationDailyRecord $hospitalizationDailyRecord)
    {
        $hospitalizationDailyRecord->load(['hospitalization.pet', 'user']);

        return view('hospitalization-daily-records.show', compact('hospitalizationDailyRecord'));
    }

    public function edit(HospitalizationDailyRecord $hospitalizationDailyRecord)
    {
        $hospitalizations = Hospitalization::with('pet')->get();
        return view('hospitalization-daily-records.edit', compact('hospitalizationDailyRecord', 'hospitalizations'));
    }

    public function update(Request $request, HospitalizationDailyRecord $hospitalizationDailyRecord)
    {
        $validated = $request->validate([
            'hospitalization_id' => 'required|exists:hospitalizations,id',
            'record_date' => 'required|date',
            'shift' => 'required|string|max:50',
            'subjective' => 'nullable|string',
            'objective' => 'nullable|string',
            'assessment' => 'nullable|string',
            'plan' => 'nullable|string',
            'temperature' => 'nullable|numeric',
            'heart_rate' => 'nullable|integer',
            'respiratory_rate' => 'nullable|integer',
            'appetite' => 'nullable|string|max:50',
            'hydration' => 'nullable|string|max:50',
            'urination' => 'nullable|string|max:50',
            'defecation' => 'nullable|string|max:50',
            'medications_given' => 'nullable|string',
            'observations' => 'nullable|string',
        ]);

        $hospitalizationDailyRecord->update($validated);

        return redirect()->route('hospitalization-daily-records.index')
            ->with('success', 'Registro diário atualizado com sucesso!');
    }

    public function destroy(HospitalizationDailyRecord $hospitalizationDailyRecord)
    {
        $hospitalizationDailyRecord->delete();

        return redirect()->route('hospitalization-daily-records.index')
            ->with('success', 'Registro diário excluído com sucesso!');
    }
}
