<?php

namespace App\Http\Controllers;

use App\Models\AnesthesiaMonitoring;
use App\Models\AnesthesiaVitalSign;
use App\Models\Surgery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnesthesiaMonitoringController extends Controller
{
    public function index(Request $request)
    {
        $query = AnesthesiaMonitoring::with(['surgery', 'pet', 'vet', 'vitalSigns']);

        if ($request->surgery_id) {
            $query->where('surgery_id', $request->surgery_id);
        }

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $monitorings = $query->orderBy('monitoring_start', 'desc')->paginate(20);

        return view('anesthesia-monitorings.index', compact('monitorings'));
    }

    public function create()
    {
        $surgeries = Surgery::with('pet')->orderBy('scheduled_date', 'desc')->get();
        return view('anesthesia-monitorings.create', compact('surgeries'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'surgery_id' => 'required|exists:surgeries,id',
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'anesthetist' => 'nullable|string|max:255',
            'anesthetic_protocol' => 'nullable|string',
            'premedication' => 'nullable|string',
            'induction_agent' => 'nullable|string',
            'maintenance_agent' => 'nullable|string',
            'iv_access' => 'nullable|string|max:100',
            'intubation_type' => 'nullable|string|max:100',
            'monitoring_start' => 'nullable|date',
            'monitoring_end' => 'nullable|date|after:monitoring_start',
            'fluid_type' => 'nullable|string|max:100',
            'fluid_rate' => 'nullable|string|max:100',
            'observations' => 'nullable|string',
            'vital_signs' => 'nullable|array',
            'vital_signs.*.recorded_at' => 'required_with:vital_signs',
            'vital_signs.*.heart_rate' => 'nullable|integer',
            'vital_signs.*.respiratory_rate' => 'nullable|integer',
            'vital_signs.*.spo2' => 'nullable|numeric|min:0|max:100',
            'vital_signs.*.etco2' => 'nullable|numeric|min:0',
            'vital_signs.*.blood_pressure_systolic' => 'nullable|integer',
            'vital_signs.*.blood_pressure_diastolic' => 'nullable|integer',
            'vital_signs.*.blood_pressure_mean' => 'nullable|integer',
            'vital_signs.*.temperature' => 'nullable|numeric',
            'vital_signs.*.anesthetic_depth' => 'nullable|string|max:50',
            'vital_signs.*.vaporizer_setting' => 'nullable|string|max:50',
            'vital_signs.*.observations' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $vitalSigns = $validated['vital_signs'] ?? [];
            unset($validated['vital_signs']);

            $monitoring = AnesthesiaMonitoring::create($validated);

            foreach ($vitalSigns as $signData) {
                $signData['anesthesia_monitoring_id'] = $monitoring->id;
                AnesthesiaVitalSign::create($signData);
            }

            DB::commit();
            return redirect()->route('anesthesia-monitorings.index')
                ->with('success', 'Monitoramento anestésico cadastrado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao cadastrar monitoramento.')->withInput();
        }
    }

    public function show(AnesthesiaMonitoring $anesthesiaMonitoring)
    {
        $anesthesiaMonitoring->load(['surgery', 'pet', 'vet', 'vitalSigns']);
        $monitoring = $anesthesiaMonitoring;
        return view('anesthesia-monitorings.show', compact('monitoring'));
    }

    public function edit(AnesthesiaMonitoring $anesthesiaMonitoring)
    {
        $anesthesiaMonitoring->load('vitalSigns');
        return view('anesthesia-monitorings.edit', compact('anesthesiaMonitoring'));
    }

    public function update(Request $request, AnesthesiaMonitoring $anesthesiaMonitoring)
    {
        $validated = $request->validate([
            'surgery_id' => 'required|exists:surgeries,id',
            'pet_id' => 'required|exists:pets,id',
            'vet_id' => 'required|exists:users,id',
            'anesthetist' => 'nullable|string|max:255',
            'anesthetic_protocol' => 'nullable|string',
            'premedication' => 'nullable|string',
            'induction_agent' => 'nullable|string',
            'maintenance_agent' => 'nullable|string',
            'iv_access' => 'nullable|string|max:100',
            'intubation_type' => 'nullable|string|max:100',
            'monitoring_start' => 'nullable|date',
            'monitoring_end' => 'nullable|date',
            'fluid_type' => 'nullable|string|max:100',
            'fluid_rate' => 'nullable|string|max:100',
            'observations' => 'nullable|string',
            'vital_signs' => 'nullable|array',
            'vital_signs.*.recorded_at' => 'required_with:vital_signs',
            'vital_signs.*.heart_rate' => 'nullable|integer',
            'vital_signs.*.respiratory_rate' => 'nullable|integer',
            'vital_signs.*.spo2' => 'nullable|numeric|min:0|max:100',
            'vital_signs.*.etco2' => 'nullable|numeric|min:0',
            'vital_signs.*.blood_pressure_systolic' => 'nullable|integer',
            'vital_signs.*.blood_pressure_diastolic' => 'nullable|integer',
            'vital_signs.*.blood_pressure_mean' => 'nullable|integer',
            'vital_signs.*.temperature' => 'nullable|numeric',
            'vital_signs.*.anesthetic_depth' => 'nullable|string|max:50',
            'vital_signs.*.vaporizer_setting' => 'nullable|string|max:50',
            'vital_signs.*.observations' => 'nullable|string',
            'vital_signs.*.id' => 'nullable|exists:anesthesia_vital_signs,id',
        ]);

        DB::beginTransaction();
        try {
            $vitalSigns = $validated['vital_signs'] ?? [];
            unset($validated['vital_signs']);

            $anesthesiaMonitoring->update($validated);

            $existingIds = [];
            foreach ($vitalSigns as $signData) {
                if (isset($signData['id'])) {
                    AnesthesiaVitalSign::where('id', $signData['id'])
                        ->where('anesthesia_monitoring_id', $anesthesiaMonitoring->id)
                        ->update($signData);
                    $existingIds[] = $signData['id'];
                } else {
                    $signData['anesthesia_monitoring_id'] = $anesthesiaMonitoring->id;
                    $sign = AnesthesiaVitalSign::create($signData);
                    $existingIds[] = $sign->id;
                }
            }

            AnesthesiaVitalSign::where('anesthesia_monitoring_id', $anesthesiaMonitoring->id)
                ->whereNotIn('id', $existingIds)
                ->delete();

            DB::commit();
            return redirect()->route('anesthesia-monitorings.index')
                ->with('success', 'Monitoramento anestésico atualizado!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao atualizar monitoramento.')->withInput();
        }
    }

    public function destroy(AnesthesiaMonitoring $anesthesiaMonitoring)
    {
        $anesthesiaMonitoring->vitalSigns()->delete();
        $anesthesiaMonitoring->delete();

        return redirect()->route('anesthesia-monitorings.index')
            ->with('success', 'Monitoramento anestésico excluído!');
    }
}
