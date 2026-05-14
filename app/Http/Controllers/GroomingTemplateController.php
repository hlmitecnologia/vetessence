<?php

namespace App\Http\Controllers;

use App\Models\GroomingTemplate;
use Illuminate\Http\Request;

class GroomingTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:servicos');
    }

    public function index()
    {
        $templates = GroomingTemplate::orderBy('name')->paginate(20);
        return view('grooming-templates.index', compact('templates'));
    }

    public function create()
    {
        return view('grooming-templates.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'nullable|string|max:50',
            'breed' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:20',
            'services' => 'nullable|json',
            'price' => 'required|numeric|min:0',
            'estimated_minutes' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['services'] = $request->services ? json_decode($request->services, true) : null;

        GroomingTemplate::create($data);

        return redirect()->route('grooming-templates.index')
            ->with('success', 'Template de banho/tosa criado.');
    }

    public function show(GroomingTemplate $groomingTemplate)
    {
        return view('grooming-templates.show', compact('groomingTemplate'));
    }

    public function edit(GroomingTemplate $groomingTemplate)
    {
        return view('grooming-templates.edit', compact('groomingTemplate'));
    }

    public function update(Request $request, GroomingTemplate $groomingTemplate)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'nullable|string|max:50',
            'breed' => 'nullable|string|max:100',
            'size' => 'nullable|string|max:20',
            'services' => 'nullable|json',
            'price' => 'required|numeric|min:0',
            'estimated_minutes' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['services'] = $request->services ? json_decode($request->services, true) : null;

        $groomingTemplate->update($data);

        return redirect()->route('grooming-templates.index')
            ->with('success', 'Template de banho/tosa atualizado.');
    }

    public function destroy(GroomingTemplate $groomingTemplate)
    {
        $groomingTemplate->delete();
        return redirect()->route('grooming-templates.index')
            ->with('success', 'Template de banho/tosa removido.');
    }
}
