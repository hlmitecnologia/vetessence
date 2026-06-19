<?php

namespace App\Http\Controllers;

use App\Models\CommunicationTemplate;
use Illuminate\Http\Request;

class CommunicationTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index(Request $request)
    {
        $query = CommunicationTemplate::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->channel) {
            $query->where('channel', $request->channel);
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('name')->paginate(20);

        return view('communication-templates.index', compact('templates'));
    }

    public function create()
    {
        return redirect()->route('communication-templates.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'channel' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        CommunicationTemplate::create($validated);

        return redirect()->route('communication-templates.index')->with('success', 'Template cadastrado com sucesso!');
    }

    public function show(CommunicationTemplate $communicationTemplate)
    {
        return view('communication-templates.show', compact('communicationTemplate'));
    }

    public function edit($communicationTemplate)
    {
        return redirect()->route('communication-templates.index');
    }

    public function update(Request $request, CommunicationTemplate $communicationTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'channel' => 'required|string|max:50',
            'subject' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $communicationTemplate->update($validated);

        return redirect()->route('communication-templates.index')->with('success', 'Template atualizado com sucesso!');
    }

    public function destroy(CommunicationTemplate $communicationTemplate)
    {
        $communicationTemplate->delete();

        return redirect()->route('communication-templates.index')->with('success', 'Template excluído!');
    }
}
