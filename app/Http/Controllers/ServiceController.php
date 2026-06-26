<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use App\Models\ServicePriceTier;
use App\Models\ServiceTypeMap;
use App\Models\Branch;
use App\Services\BranchContext;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin');
    }

    public function index(Request $request)
    {
        $query = Service::with('category', 'priceTiers');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->orderBy('name')->get();

        $categories = Category::where('type', 'service')->orderBy('name')->get();

        $medicalTypes = ['consulta', 'cirurgia', 'emergencia', 'vacina', 'retorno', 'exame'];
        $branchId = BranchContext::hasBranch() ? BranchContext::get() : null;
        $typeMaps = ServiceTypeMap::with('service')
            ->where(function ($q) use ($branchId) {
                $q->whereNull('branch_id')
                  ->orWhere('branch_id', $branchId);
            })
            ->get()
            ->keyBy('type');

        return view('services.index', compact('services', 'categories', 'medicalTypes', 'typeMaps', 'branchId'));
    }

    public function create()
    {
        return redirect()->route('services.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'tiers' => 'nullable|array',
            'tiers.*.species' => 'required|string|max:50',
            'tiers.*.size' => 'nullable|string|max:30',
            'tiers.*.price' => 'required|numeric|min:0',
        ]);

        $service = Service::create($validated);

        if ($request->tiers) {
            foreach ($request->tiers as $tier) {
                $service->priceTiers()->create($tier);
            }
        }

        return redirect()->route('services.index')->with('success', 'Serviço cadastrado com sucesso!');
    }

    public function show(Service $service)
    {
        $service->load('priceTiers');
        return view('services.show', compact('service'));
    }

    public function edit($service)
    {
        return redirect()->route('services.index');
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'tiers' => 'nullable|array',
            'tiers.*.species' => 'required|string|max:50',
            'tiers.*.size' => 'nullable|string|max:30',
            'tiers.*.price' => 'required|numeric|min:0',
        ]);

        $service->update($validated);

        if ($request->tiers) {
            $service->priceTiers()->delete();
            foreach ($request->tiers as $tier) {
                $service->priceTiers()->create($tier);
            }
        }

        return redirect()->route('services.index')->with('success', 'Serviço atualizado!');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Serviço excluído!');
    }

    public function updateTypeMap(Request $request, string $type)
    {
        $validated = $request->validate([
            'service_id' => 'nullable|exists:services,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        if ($validated['service_id']) {
            ServiceTypeMap::updateOrCreate(
                ['type' => $type, 'branch_id' => $validated['branch_id'] ?? null],
                ['service_id' => $validated['service_id']]
            );
        } else {
            ServiceTypeMap::where('type', $type)
                ->where('branch_id', $validated['branch_id'] ?? null)
                ->delete();
        }

        return redirect()->route('services.index')->with('success', 'Mapeamento atualizado!');
    }
}
