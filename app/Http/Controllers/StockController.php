<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Branch;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:stock.view')->only(['movements']);
        $this->middleware('can:stock.create')->only(['create', 'store']);
        $this->middleware('can:stock.transfer')->only(['transferForm', 'transfer']);
    }

    public function movements(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        if ($request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(30);

        $products = Product::orderBy('name')->get();

        return view('stock.movements', compact('movements', 'products'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('stock.create', compact('products', 'branches'));
    }

    public function store(Request $request)
    {
        $rules = [
            'product_id' => 'required|exists:products,id',
            'type'       => 'required|in:entry,exit,adjustment,loss,return,transfer',
            'quantity'   => 'required|numeric|min:0.01',
            'batch_number' => 'nullable|string|max:100',
            'expiry_date'  => 'nullable|date',
            'notes'        => 'nullable|string',
        ];

        if ($request->type === 'transfer') {
            $rules['from_branch_id'] = 'required|exists:branches,id';
            $rules['to_branch_id'] = 'required|exists:branches,id|different:from_branch_id';
        } else {
            $rules['branch_id'] = 'required|exists:branches,id';
        }

        $data = $request->validate($rules);

        $product = Product::findOrFail($data['product_id']);

        if ($data['type'] === 'transfer') {
            if ($data['quantity'] > $product->stock) {
                return back()->withErrors(['quantity' => 'Quantidade superior ao estoque disponível do produto.'])->withInput();
            }
            if ($data['from_branch_id'] === $data['to_branch_id']) {
                return back()->withErrors(['to_branch_id' => 'A unidade de destino deve ser diferente da origem.'])->withInput();
            }

            StockMovement::create([
                'product_id' => $data['product_id'],
                'quantity'   => $data['quantity'],
                'type'       => 'transfer_out',
                'branch_id'  => $data['from_branch_id'],
                'user_id'    => auth()->id(),
                'notes'      => "Transferido para filial #{$data['to_branch_id']}. {$data['notes']}",
            ]);

            StockMovement::create([
                'product_id' => $data['product_id'],
                'quantity'   => $data['quantity'],
                'type'       => 'transfer_in',
                'branch_id'  => $data['to_branch_id'],
                'user_id'    => auth()->id(),
                'notes'      => "Recebido da filial #{$data['from_branch_id']}. {$data['notes']}",
            ]);

            return redirect()->route('stock.movements')
                ->with('success', 'Transferência realizada com sucesso.');
        }

        $movement = StockMovement::create([
            'product_id'  => $data['product_id'],
            'type'        => $data['type'],
            'quantity'    => $data['quantity'],
            'branch_id'   => $data['branch_id'],
            'batch_number' => $data['batch_number'] ?? null,
            'expiry_date'  => $data['expiry_date'] ?? null,
            'user_id'     => auth()->id(),
            'notes'       => $data['notes'] ?? null,
        ]);

        if (in_array($data['type'], ['entry', 'return'])) {
            $product->increment('stock', $data['quantity']);
        } elseif (in_array($data['type'], ['exit', 'loss', 'adjustment'])) {
            $qty = min($data['quantity'], $product->stock);
            $product->decrement('stock', $qty);
            $movement->update(['quantity' => $qty]);
        }

        return redirect()->route('stock.movements')
            ->with('success', 'Movimentação registrada com sucesso.');
    }

    public function transferForm()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        return view('stock.transfer', compact('products', 'branches'));
    }

    public function transfer(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'notes' => 'nullable|string',
        ]);

        StockMovement::create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'type' => 'transfer_out',
            'branch_id' => $data['from_branch_id'],
            'user_id' => auth()->id(),
            'notes' => "Transferido para filial #{$data['to_branch_id']}. {$data['notes']}",
        ]);

        StockMovement::create([
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
            'type' => 'transfer_in',
            'branch_id' => $data['to_branch_id'],
            'user_id' => auth()->id(),
            'notes' => "Recebido da filial #{$data['from_branch_id']}. {$data['notes']}",
        ]);

        return redirect()->route('stock.movements')
            ->with('success', 'Transferência realizada com sucesso.');
    }
}
