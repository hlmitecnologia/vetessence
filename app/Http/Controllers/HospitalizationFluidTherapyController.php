<?php

namespace App\Http\Controllers;

use App\Models\Hospitalization;
use App\Models\HospitalizationFluidTherapy;
use Illuminate\Http\Request;

class HospitalizationFluidTherapyController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:hospitalizacao');
    }

    public function store(Request $request, Hospitalization $hospitalization)
    {
        $validated = $request->validate([
            'fluid_type' => 'required|string|max:100',
            'rate' => 'nullable|numeric|min:0',
            'volume' => 'nullable|numeric|min:0',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after:start_time',
            'route' => 'nullable|string|max:20',
            'observations' => 'nullable|string|max:500',
        ]);

        $validated['hospitalization_id'] = $hospitalization->id;

        HospitalizationFluidTherapy::create($validated);

        return redirect()->route('hospitalizations.show', $hospitalization)
            ->with('success', 'Fluidoterapia cadastrada com sucesso!');
    }

    public function destroy(Hospitalization $hospitalization, HospitalizationFluidTherapy $fluidTherapy)
    {
        $fluidTherapy->delete();

        return redirect()->route('hospitalizations.show', $hospitalization)
            ->with('success', 'Fluidoterapia excluída com sucesso!');
    }
}
