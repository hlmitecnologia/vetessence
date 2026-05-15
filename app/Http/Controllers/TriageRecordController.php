<?php

namespace App\Http\Controllers;

use App\Models\TriageRecord;
use App\Models\Pet;
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
        return view('triage.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'severity' => 'required|in:green,yellow,orange,red',
            'chief_complaint' => 'required|string',
            'vital_signs' => 'nullable|array',
            'assigned_vet_id' => 'nullable|exists:users,id',
        ]);

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
        return view('triage.edit', compact('triage', 'pets'));
    }

    public function update(Request $request, TriageRecord $triage)
    {
        $data = $request->validate([
            'severity' => 'required|in:green,yellow,orange,red',
            'status' => 'required|in:waiting,in_consultation,seen,discharged',
            'assigned_vet_id' => 'nullable|exists:users,id',
            'chief_complaint' => 'nullable|string',
            'vital_signs' => 'nullable|array',
        ]);

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
