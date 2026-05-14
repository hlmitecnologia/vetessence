<?php

namespace App\Http\Controllers;

use App\Models\DrugInteraction;
use App\Services\DrugInteractionService;
use Illuminate\Http\Request;

class DrugInteractionController extends Controller
{
    protected $interactionService;

    public function __construct(DrugInteractionService $interactionService)
    {
        $this->interactionService = $interactionService;
    }

    public function index(Request $request)
    {
        $query = DrugInteraction::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('drug_a', 'like', "%{$request->search}%")
                  ->orWhere('drug_b', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->severity) {
            $query->where('severity', $request->severity);
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $interactions = $query->orderBy('drug_a')->orderBy('drug_b')->paginate(20);

        return view('drug-interactions.index', compact('interactions'));
    }

    public function create()
    {
        return view('drug-interactions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'drug_a' => 'required|string|max:255',
            'drug_b' => 'required|string|max:255|different:drug_a',
            'severity' => 'required|in:contraindicated,caution,minor',
            'description' => 'required|string',
            'mechanism' => 'nullable|string|max:255',
            'management' => 'nullable|string',
            'source' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['drug_a'] = trim($validated['drug_a']);
        $validated['drug_b'] = trim($validated['drug_b']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $existing = DrugInteraction::where(function ($q) use ($validated) {
            $q->where(function ($q2) use ($validated) {
                $q2->where('drug_a', $validated['drug_a'])->where('drug_b', $validated['drug_b']);
            })->orWhere(function ($q2) use ($validated) {
                $q2->where('drug_a', $validated['drug_b'])->where('drug_b', $validated['drug_a']);
            });
        })->exists();

        if ($existing) {
            return back()->withInput()->with('error', 'Esta interação já está cadastrada.');
        }

        DrugInteraction::create($validated);

        return redirect()->route('drug-interactions.index')
            ->with('success', 'Interação medicamentosa cadastrada com sucesso!');
    }

    public function show(DrugInteraction $drugInteraction)
    {
        return view('drug-interactions.show', compact('drugInteraction'));
    }

    public function edit(DrugInteraction $drugInteraction)
    {
        return view('drug-interactions.edit', compact('drugInteraction'));
    }

    public function update(Request $request, DrugInteraction $drugInteraction)
    {
        $validated = $request->validate([
            'drug_a' => 'required|string|max:255',
            'drug_b' => 'required|string|max:255|different:drug_a',
            'severity' => 'required|in:contraindicated,caution,minor',
            'description' => 'required|string',
            'mechanism' => 'nullable|string|max:255',
            'management' => 'nullable|string',
            'source' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['drug_a'] = trim($validated['drug_a']);
        $validated['drug_b'] = trim($validated['drug_b']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $existing = DrugInteraction::where(function ($q) use ($validated) {
            $q->where(function ($q2) use ($validated) {
                $q2->where('drug_a', $validated['drug_a'])->where('drug_b', $validated['drug_b']);
            })->orWhere(function ($q2) use ($validated) {
                $q2->where('drug_a', $validated['drug_b'])->where('drug_b', $validated['drug_a']);
            });
        })->where('id', '!=', $drugInteraction->id)->exists();

        if ($existing) {
            return back()->withInput()->with('error', 'Esta interação já está cadastrada.');
        }

        $drugInteraction->update($validated);

        return redirect()->route('drug-interactions.index')
            ->with('success', 'Interação medicamentosa atualizada com sucesso!');
    }

    public function destroy(DrugInteraction $drugInteraction)
    {
        $drugInteraction->delete();

        return redirect()->route('drug-interactions.index')
            ->with('success', 'Interação medicamentosa excluída com sucesso!');
    }

    public function checkApi(Request $request)
    {
        $request->validate([
            'drugs' => 'required|array|min:2',
            'drugs.*' => 'required|string|max:255',
        ]);

        $interactions = $this->interactionService->check($request->drugs);

        return response()->json([
            'has_interactions' => $interactions->isNotEmpty(),
            'interactions' => $interactions->map(function ($i) {
                return [
                    'drug_a' => $i->drug_a,
                    'drug_b' => $i->drug_b,
                    'severity' => $i->severity,
                    'description' => $i->description,
                    'management' => $i->management,
                ];
            }),
        ]);
    }
}
