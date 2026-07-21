<?php

namespace App\Http\Controllers;

use App\Models\PetShopPackage;
use App\Models\Branch;
use App\Models\Service;
use Illuminate\Http\Request;

class PetShopPackageController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:pet-shop-packages.view')->only(['index', 'create', 'edit']);
        $this->middleware('can:pet-shop-packages.create')->only(['store']);
        $this->middleware('can:pet-shop-packages.edit')->only(['update']);
        $this->middleware('can:pet-shop-packages.delete')->only(['destroy']);
    }

    public function index()
    {
        $packages = PetShopPackage::with('branch')->orderBy('name')->get();
        return view('pet-shop-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('pet-shop-packages.create');
    }

    public function edit(PetShopPackage $petShopPackage)
    {
        return view('pet-shop-packages.edit', compact('petShopPackage'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:grooming,boarding,both',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required_with:services|exists:services,id',
            'services.*.qty' => 'required_with:services|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'max_uses' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $data['services'] = json_encode($data['services'] ?? []);
        $data['is_active'] = $request->boolean('is_active', true);

        PetShopPackage::create($data);

        return redirect()->route('pet-shop-packages.index')
            ->with('success', 'Pacote cadastrado com sucesso.');
    }

    public function update(Request $request, PetShopPackage $petShopPackage)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:grooming,boarding,both',
            'services' => 'nullable|array',
            'services.*.service_id' => 'required_with:services|exists:services,id',
            'services.*.qty' => 'required_with:services|integer|min:1',
            'total_price' => 'required|numeric|min:0',
            'original_price' => 'required|numeric|min:0',
            'validity_days' => 'required|integer|min:1',
            'max_uses' => 'required|integer|min:1',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $data['services'] = json_encode($data['services'] ?? []);
        $data['is_active'] = $request->boolean('is_active', true);

        $petShopPackage->update($data);

        return redirect()->route('pet-shop-packages.index')
            ->with('success', 'Pacote atualizado com sucesso.');
    }

    public function destroy(PetShopPackage $petShopPackage)
    {
        if ($petShopPackage->subscriptions()->count() > 0) {
            return back()->with('error', 'Não é possível excluir pacote com assinaturas ativas.');
        }

        $petShopPackage->delete();

        return redirect()->route('pet-shop-packages.index')
            ->with('success', 'Pacote excluído.');
    }
}
