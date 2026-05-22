<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('cnpj', 'like', "%{$request->search}%");
        }

        $suppliers = $query->orderBy('name')->paginate(20);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'ie' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'complement' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'contact' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zipcode' => 'nullable|string|max:20',
        ]);

        Supplier::create($validated);

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor cadastrado!');
    }

    public function show(Supplier $supplier)
    {
        $supplier->load('products');
        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier)
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cnpj' => 'nullable|string|max:18',
            'ie' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'number' => 'nullable|string|max:20',
            'neighborhood' => 'nullable|string|max:100',
            'complement' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'contact' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'state_id' => 'nullable|exists:states,id',
            'city_id' => 'nullable|exists:cities,id',
            'zipcode' => 'nullable|string|max:20',
        ]);

        $supplier->update($validated);

        return redirect()->route('suppliers.index')->with('success', 'Fornecedor atualizado!');
    }

    public function destroy(Supplier $supplier)
    {
        if ($supplier->products()->count() > 0) {
            return back()->with('error', 'Fornecedor possui produtos vinculados.');
        }

        $supplier->delete();
        return redirect()->route('suppliers.index')->with('success', 'Fornecedor excluído!');
    }
}
