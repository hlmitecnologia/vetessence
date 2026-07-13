<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:unidades');
    }
    public function index()
    {
        $branches = Branch::orderBy('name')->get();
        return view('branches.index', compact('branches'));
    }

    public function create()
    {
        return view('branches.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:branches,slug',
            'address' => 'nullable|string|max:500',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'complement' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cnpj' => 'nullable|string|max:20',
            'municipio_ibge' => 'nullable|string|max:7',
            'regime_tributario' => 'nullable|in:mei,simples_nacional,lucro_presumido',
            'serie' => 'nullable|string|max:3',
            'im' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_main' => 'boolean',
            'notes' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_main'] = $request->boolean('is_main', false);

        if ($validated['is_main']) {
            Branch::where('is_main', true)->update(['is_main' => false]);
        }

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Unidade cadastrada com sucesso!');
    }

    public function show(Branch $branch)
    {
        $branch->load('users');
        return view('branches.show', compact('branch'));
    }

    public function edit(Branch $branch)
    {
        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:100|unique:branches,slug,' . $branch->id,
            'address' => 'nullable|string|max:500',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'complement' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:50',
            'zip_code' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'cnpj' => 'nullable|string|max:20',
            'municipio_ibge' => 'nullable|string|max:7',
            'regime_tributario' => 'nullable|in:mei,simples_nacional,lucro_presumido',
            'serie' => 'nullable|string|max:3',
            'im' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'is_main' => 'boolean',
            'notes' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['is_main'] = $request->boolean('is_main', false);

        if ($validated['is_main']) {
            Branch::where('is_main', true)->where('id', '!=', $branch->id)->update(['is_main' => false]);
        }

        $branch->update($validated);

        return redirect()->route('branches.index')->with('success', 'Unidade atualizada!');
    }

    public function destroy(Branch $branch)
    {
        if ($branch->users()->count() > 0) {
            return back()->with('error', 'Remova os usuários vinculados antes de excluir.');
        }
        $branch->delete();
        return redirect()->route('branches.index')->with('success', 'Unidade excluída.');
    }
}
