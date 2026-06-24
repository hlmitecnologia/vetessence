<?php

namespace App\Http\Controllers;

use App\Models\ClinicalReportTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClinicalReportTemplateController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:modelo-laudo');
    }
    public function index(Request $request)
    {
        $query = ClinicalReportTemplate::query();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('description', 'like', "%{$request->search}%");
            });
        }

        if ($request->species) {
            $query->where('species', $request->species);
        }

        if ($request->specialty) {
            $query->where('specialty', $request->specialty);
        }

        if ($request->is_active !== null) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $templates = $query->orderBy('name')->get();

        return view('clinical-report-templates.index', compact('templates'));
    }

    public function create()
    {
        return redirect()->route('clinical-report-templates.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:clinical_report_templates,slug',
            'species' => 'nullable|string|max:50',
            'specialty' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        ClinicalReportTemplate::create($validated);

        return redirect()->route('clinical-report-templates.index')
            ->with('success', 'Modelo de laudo cadastrado com sucesso!');
    }

    public function show(ClinicalReportTemplate $clinicalReportTemplate)
    {
        return view('clinical-report-templates.show', compact('clinicalReportTemplate'));
    }

    public function edit($clinicalReportTemplate)
    {
        return redirect()->route('clinical-report-templates.index');
    }

    public function update(Request $request, ClinicalReportTemplate $clinicalReportTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:clinical_report_templates,slug,' . $clinicalReportTemplate->id,
            'species' => 'nullable|string|max:50',
            'specialty' => 'nullable|string|max:100',
            'category' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'content' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $validated['slug'] = $validated['slug'] ?? Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        $clinicalReportTemplate->update($validated);

        return redirect()->route('clinical-report-templates.index')
            ->with('success', 'Modelo de laudo atualizado com sucesso!');
    }

    public function destroy(ClinicalReportTemplate $clinicalReportTemplate)
    {
        $clinicalReportTemplate->delete();

        return redirect()->route('clinical-report-templates.index')
            ->with('success', 'Modelo de laudo excluído com sucesso!');
    }
}
