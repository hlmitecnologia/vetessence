<?php

namespace App\Http\Controllers;

use App\Models\LabEquipmentIntegration;
use App\Models\LabEquipmentResult;
use Illuminate\Http\Request;

class LabEquipmentIntegrationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:integracao-equipamentos');
    }
    public function index(Request $request)
    {
        $integrations = LabEquipmentIntegration::orderBy('name')->paginate(20);
        return view('lab-equipment-integrations.index', compact('integrations'));
    }

    public function create()
    {
        return view('lab-equipment-integrations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_type' => 'required|string|max:100',
            'protocol' => 'required|string|max:50',
            'endpoint_url' => 'nullable|string|max:500',
            'api_key' => 'nullable|string|max:255',
            'ip_address' => 'nullable|string|max:45',
            'port' => 'nullable|integer|min:1|max:65535',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        LabEquipmentIntegration::create($validated);

        return redirect()->route('lab-equipment-integrations.index')
            ->with('success', 'Integração cadastrada com sucesso!');
    }

    public function show(LabEquipmentIntegration $labEquipmentIntegration)
    {
        $labEquipmentIntegration->load('results.pet');
        return view('lab-equipment-integrations.show', compact('labEquipmentIntegration'));
    }

    public function edit(LabEquipmentIntegration $labEquipmentIntegration)
    {
        return view('lab-equipment-integrations.edit', compact('labEquipmentIntegration'));
    }

    public function update(Request $request, LabEquipmentIntegration $labEquipmentIntegration)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'equipment_type' => 'required|string|max:100',
            'protocol' => 'required|string|max:50',
            'endpoint_url' => 'nullable|string|max:500',
            'api_key' => 'nullable|string|max:255',
            'ip_address' => 'nullable|string|max:45',
            'port' => 'nullable|integer|min:1|max:65535',
            'is_active' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $labEquipmentIntegration->update($validated);

        return redirect()->route('lab-equipment-integrations.index')
            ->with('success', 'Integração atualizada com sucesso!');
    }

    public function destroy(LabEquipmentIntegration $labEquipmentIntegration)
    {
        $labEquipmentIntegration->delete();
        return redirect()->route('lab-equipment-integrations.index')
            ->with('success', 'Integração excluída.');
    }
}
