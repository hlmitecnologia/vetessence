<?php

namespace App\Http\Controllers;

use App\Models\BreedDefault;
use Illuminate\Http\Request;

class BreedDefaultController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:configuracoes');
    }

    public function index()
    {
        $defaults = BreedDefault::orderBy('species')->orderBy('breed')->paginate(20);
        return view('breed-defaults.index', compact('defaults'));
    }

    public function create()
    {
        return view('breed-defaults.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'species' => 'required|string|max:50',
            'breed' => 'required|string|max:100',
            'size' => 'nullable|string|max:20',
            'avg_weight_min' => 'nullable|numeric|min:0',
            'avg_weight_max' => 'nullable|numeric|min:0',
            'avg_lifespan_min' => 'nullable|integer|min:0',
            'avg_lifespan_max' => 'nullable|integer|min:0',
            'temperament' => 'nullable|string|max:255',
            'predispositions' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        BreedDefault::create($data);

        return redirect()->route('breed-defaults.index')
            ->with('success', 'Padrão de raça criado.');
    }

    public function show(BreedDefault $breedDefault)
    {
        return view('breed-defaults.show', compact('breedDefault'));
    }

    public function edit(BreedDefault $breedDefault)
    {
        return view('breed-defaults.edit', compact('breedDefault'));
    }

    public function update(Request $request, BreedDefault $breedDefault)
    {
        $data = $request->validate([
            'species' => 'required|string|max:50',
            'breed' => 'required|string|max:100',
            'size' => 'nullable|string|max:20',
            'avg_weight_min' => 'nullable|numeric|min:0',
            'avg_weight_max' => 'nullable|numeric|min:0',
            'avg_lifespan_min' => 'nullable|integer|min:0',
            'avg_lifespan_max' => 'nullable|integer|min:0',
            'temperament' => 'nullable|string|max:255',
            'predispositions' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');

        $breedDefault->update($data);

        return redirect()->route('breed-defaults.index')
            ->with('success', 'Padrão de raça atualizado.');
    }

    public function destroy(BreedDefault $breedDefault)
    {
        $breedDefault->delete();
        return redirect()->route('breed-defaults.index')
            ->with('success', 'Padrão de raça removido.');
    }
}
