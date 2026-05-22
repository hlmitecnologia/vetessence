<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Category;
use App\Models\ServicePriceTier;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Service::with('category', 'priceTiers');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->orderBy('name')->paginate(20);

        $categories = Category::where('type', 'service')->orderBy('name')->get();

        return view('services.index', compact('services', 'categories'));
    }

    public function create()
    {
        $categories = Category::where('type', 'service')->orderBy('name')->get();
        $speciesList = config('species');
        return view('services.create', compact('categories', 'speciesList'));
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

    public function edit(Service $service)
    {
        $categories = Category::where('type', 'service')->orderBy('name')->get();
        $service->load('priceTiers');
        $speciesList = config('species');
        return view('services.edit', compact('service', 'categories', 'speciesList'));
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
}
