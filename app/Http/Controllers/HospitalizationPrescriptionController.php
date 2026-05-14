<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\HospitalizationPrescription;
use Illuminate\Http\Request;

class HospitalizationPrescriptionController extends Controller
{
    public function store(Request $request, Hospitalization $hospitalization)
    {
        $validated = $request->validate([
            'medication' => 'required|string|max:255',
            'dosage' => 'required|string|max:50',
            'unit' => 'nullable|string|max:20',
            'frequency' => 'required|string|max:100',
            'route' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'notes' => 'nullable|string|max:1000',
        ]);

        $validated['hospitalization_id'] = $hospitalization->id;
        $validated['prescribed_by'] = auth()->id();
        $validated['status'] = 'active';

        HospitalizationPrescription::create($validated);

        return redirect()->route('hospitalizations.show', $hospitalization)
            ->with('success', 'Prescrição cadastrada com sucesso!');
    }

    public function update(Request $request, Hospitalization $hospitalization, HospitalizationPrescription $prescription)
    {
        $validated = $request->validate([
            'medication' => 'required|string|max:255',
            'dosage' => 'required|string|max:50',
            'unit' => 'nullable|string|max:20',
            'frequency' => 'required|string|max:100',
            'route' => 'nullable|string|max:50',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'nullable|string|in:active,discontinued,completed',
            'notes' => 'nullable|string|max:1000',
        ]);

        $prescription->update($validated);

        return redirect()->route('hospitalizations.show', $hospitalization)
            ->with('success', 'Prescrição atualizada com sucesso!');
    }

    public function destroy(Hospitalization $hospitalization, HospitalizationPrescription $prescription)
    {
        $prescription->delete();

        return redirect()->route('hospitalizations.show', $hospitalization)
            ->with('success', 'Prescrição excluída com sucesso!');
    }
}
