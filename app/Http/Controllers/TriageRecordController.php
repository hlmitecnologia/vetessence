<?php

namespace App\Http\Controllers;

use App\Models\TriageRecord;
use App\Models\Pet;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class TriageRecordController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:triage.view')->only(['index', 'show']);
        $this->middleware('can:triage.create')->only(['create', 'store']);
        $this->middleware('can:triage.edit')->only(['edit', 'update']);
        $this->middleware('can:triage.delete')->only(['destroy']);
    }

    public function index()
    {
        $waiting = TriageRecord::with(['pet', 'assignedVet'])
            ->whereIn('status', ['waiting', 'in_consultation'])
            ->orderBy('severity', 'desc')
            ->orderBy('check_in_at')
            ->get();
        $history = TriageRecord::with(['pet'])
            ->whereIn('status', ['seen', 'discharged'])
            ->latest()
            ->paginate(20);
        return view('triage.index', compact('waiting', 'history'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))->orderBy('name')->get();
        return view('triage.create', compact('pets', 'veterinarians'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'severity' => 'required|in:green,yellow,orange,red',
            'chief_complaint' => 'required|string',
            'assigned_vet_id' => 'nullable|exists:users,id',
            'vs_temperature' => 'nullable|string|max:20',
            'vs_heart_rate' => 'nullable|string|max:20',
            'vs_respiratory_rate' => 'nullable|string|max:20',
            'vs_weight' => 'nullable|string|max:20',
            'vs_mucosa' => 'nullable|string|max:50',
            'vs_hydration' => 'nullable|string|max:50',
            'vs_lymph_nodes' => 'nullable|string|max:50',
        ]);

        $vitalSigns = array_filter([
            'temperature' => $request->vs_temperature,
            'heart_rate' => $request->vs_heart_rate,
            'respiratory_rate' => $request->vs_respiratory_rate,
            'weight' => $request->vs_weight,
            'mucosa' => $request->vs_mucosa,
            'hydration' => $request->vs_hydration,
            'lymph_nodes' => $request->vs_lymph_nodes,
        ]);

        $data['vital_signs'] = !empty($vitalSigns) ? $vitalSigns : null;
        $data['check_in_at'] = now();
        $data['status'] = 'waiting';
        TriageRecord::create($data);
        return redirect()->route('triage.index')
            ->with('success', 'Paciente adicionado à triagem.');
    }

    public function show(TriageRecord $triage)
    {
        $triage->load(['pet', 'assignedVet', 'triageVet']);
        return view('triage.show', compact('triage'));
    }

    public function edit(TriageRecord $triage)
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        $veterinarians = User::where('is_active', true)->where(fn($q) => $q->whereHas('role', fn($q) => $q->where('slug', 'veterinario'))->orWhere('is_veterinarian', true))->orderBy('name')->get();
        return view('triage.edit', compact('triage', 'pets', 'veterinarians'));
    }

    public function update(Request $request, TriageRecord $triage)
    {
        $data = $request->validate([
            'severity' => 'required|in:green,yellow,orange,red',
            'status' => 'required|in:waiting,in_consultation,seen,discharged',
            'assigned_vet_id' => 'nullable|exists:users,id',
            'chief_complaint' => 'nullable|string',
            'vs_temperature' => 'nullable|string|max:20',
            'vs_heart_rate' => 'nullable|string|max:20',
            'vs_respiratory_rate' => 'nullable|string|max:20',
            'vs_weight' => 'nullable|string|max:20',
            'vs_mucosa' => 'nullable|string|max:50',
            'vs_hydration' => 'nullable|string|max:50',
            'vs_lymph_nodes' => 'nullable|string|max:50',
        ]);

        $vitalSigns = array_filter([
            'temperature' => $request->vs_temperature,
            'heart_rate' => $request->vs_heart_rate,
            'respiratory_rate' => $request->vs_respiratory_rate,
            'weight' => $request->vs_weight,
            'mucosa' => $request->vs_mucosa,
            'hydration' => $request->vs_hydration,
            'lymph_nodes' => $request->vs_lymph_nodes,
        ]);

        $data['vital_signs'] = !empty($vitalSigns) ? $vitalSigns : null;

        if ($data['status'] === 'seen' && !$triage->seen_at) {
            $data['seen_at'] = now();
            $data['triage_vet_id'] = auth()->id();
        }
        if ($data['status'] === 'discharged') {
            $data['discharged_at'] = now();
        }

        $triage->update($data);
        return redirect()->route('triage.index')
            ->with('success', 'Triagem atualizada.');
    }

    public function destroy(TriageRecord $triage)
    {
        $triage->delete();
        return redirect()->route('triage.index')
            ->with('success', 'Registro de triagem removido.');
    }
}
