<?php

namespace App\Http\Controllers;

use App\Models\ConsentTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsentTemplateController extends Controller
{
    public function index(Request $request)
    {
        $query = ConsentTemplate::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('name')->paginate(20);

        return view('consent-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('consent-templates.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:consent_templates,slug',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ConsentTemplate::create($validated);

        return redirect()->route('consent-templates.index')->with('success', 'Template cadastrado com sucesso!');
    }

    public function show(ConsentTemplate $consentTemplate)
    {
        $consentTemplate->load('consentForms');
        return view('consent-templates.show', compact('consentTemplate'));
    }

    public function edit(ConsentTemplate $consentTemplate)
    {
        return view('consent-templates.edit', compact('consentTemplate'));
    }

    public function update(Request $request, ConsentTemplate $consentTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:consent_templates,slug,' . $consentTemplate->id,
            'description' => 'nullable|string',
            'content' => 'required|string',
            'category' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $consentTemplate->update($validated);

        return redirect()->route('consent-templates.index')->with('success', 'Template atualizado com sucesso!');
    }

    public function destroy(ConsentTemplate $consentTemplate)
    {
        if ($consentTemplate->consentForms()->count() > 0) {
            return back()->with('error', 'Não é possível excluir template vinculado a termos de consentimento.');
        }

        $consentTemplate->delete();

        return redirect()->route('consent-templates.index')->with('success', 'Template excluído!');
    }
}
