<?php

namespace App\Http\Controllers;

use App\Models\ControlledSubstance;
use Illuminate\Http\Request;

class ControlledSubstanceController extends Controller
{
    public function index(Request $request)
    {
        $query = ControlledSubstance::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('active_ingredient', 'like', "%{$request->search}%");
            });
        }

        if ($request->schedule) {
            $query->where('schedule', $request->schedule);
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $substances = $query->orderBy('name')->paginate(20);

        return view('controlled-substances.index', compact('substances'));
    }

    public function create()
    {
        return view('controlled-substances.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'schedule' => 'required|string|max:10',
            'anvisa_register' => 'nullable|string|max:50',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        ControlledSubstance::create($validated);

        return redirect()->route('controlled-substances.index')->with('success', 'Substância controlada cadastrada!');
    }

    public function show(ControlledSubstance $controlledSubstance)
    {
        $controlledSubstance->load('logs.user');
        return view('controlled-substances.show', compact('controlledSubstance'));
    }

    public function edit(ControlledSubstance $controlledSubstance)
    {
        return view('controlled-substances.edit', compact('controlledSubstance'));
    }

    public function update(Request $request, ControlledSubstance $controlledSubstance)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'active_ingredient' => 'nullable|string|max:255',
            'schedule' => 'required|string|max:10',
            'anvisa_register' => 'nullable|string|max:50',
            'unit' => 'required|string|max:50',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $controlledSubstance->update($validated);

        return redirect()->route('controlled-substances.index')->with('success', 'Substância controlada atualizada!');
    }

    public function destroy(ControlledSubstance $controlledSubstance)
    {
        if ($controlledSubstance->logs()->count() > 0) {
            return back()->with('error', 'Não é possível excluir substância com movimentações registradas.');
        }

        $controlledSubstance->delete();

        return redirect()->route('controlled-substances.index')->with('success', 'Substância controlada excluída!');
    }
}
