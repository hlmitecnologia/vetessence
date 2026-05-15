<?php

namespace App\Http\Controllers;

use App\Models\PreAnestheticEvaluation;
use App\Models\Pet;
use Illuminate\Http\Request;

class PreAnestheticEvaluationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pre-anesthetic.view')->only(['index', 'show']);
        $this->middleware('can:pre-anesthetic.create')->only(['create', 'store']);
        $this->middleware('can:pre-anesthetic.edit')->only(['edit', 'update']);
        $this->middleware('can:pre-anesthetic.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = PreAnestheticEvaluation::with(['pet', 'vet']);
        if ($request->pet_id) $query->where('pet_id', $request->pet_id);
        if ($request->status) $query->where('status', $request->status);
        $evaluations = $query->latest()->paginate(20);
        return view('pre-anesthetic-evaluations.index', compact('evaluations'));
    }

    public function create()
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('pre-anesthetic-evaluations.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'asa_score' => 'required|integer|in:1,2,3,4,5,6',
            'fasted' => 'boolean',
            'hydrated' => 'boolean',
            'exam_checklist' => 'nullable|array',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $data['vet_id'] = auth()->id();
        $data['fasted'] = $request->boolean('fasted');
        $data['hydrated'] = $request->boolean('hydrated');

        PreAnestheticEvaluation::create($data);
        return redirect()->route('pre-anesthetic-evaluations.index')
            ->with('success', 'Avaliação pré-anestésica cadastrada.');
    }

    public function show(PreAnestheticEvaluation $preAnestheticEvaluation)
    {
        $preAnestheticEvaluation->load(['pet', 'vet', 'surgery']);
        return view('pre-anesthetic-evaluations.show', compact('preAnestheticEvaluation'));
    }

    public function edit(PreAnestheticEvaluation $preAnestheticEvaluation)
    {
        $pets = Pet::with('tutors')->orderBy('name')->get();
        return view('pre-anesthetic-evaluations.edit', compact('preAnestheticEvaluation', 'pets'));
    }

    public function update(Request $request, PreAnestheticEvaluation $preAnestheticEvaluation)
    {
        $data = $request->validate([
            'asa_score' => 'required|integer|in:1,2,3,4,5,6',
            'fasted' => 'boolean',
            'hydrated' => 'boolean',
            'exam_checklist' => 'nullable|array',
            'observations' => 'nullable|string',
            'recommendations' => 'nullable|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $data['fasted'] = $request->boolean('fasted');
        $data['hydrated'] = $request->boolean('hydrated');

        $preAnestheticEvaluation->update($data);
        return redirect()->route('pre-anesthetic-evaluations.index')
            ->with('success', 'Avaliação atualizada.');
    }

    public function destroy(PreAnestheticEvaluation $preAnestheticEvaluation)
    {
        $preAnestheticEvaluation->delete();
        return redirect()->route('pre-anesthetic-evaluations.index')
            ->with('success', 'Avaliação removida.');
    }
}
