<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Branch;
use App\Services\Nfe\NfeService;
use App\Services\StockForecastService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:stock.view')->only(['movements', 'dashboard', 'reorderSuggestions', 'expiring']);
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

        $movements = $query->orderBy('created_at', 'desc')->get();

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

            $currentStock = $product->stock;

            StockMovement::create([
                'product_id'   => $data['product_id'],
                'quantity'     => $data['quantity'],
                'type'         => 'transfer_out',
                'branch_id'    => $data['from_branch_id'],
                'user_id'      => auth()->id(),
                'notes'        => "Transferido para filial #{$data['to_branch_id']}. " . ($data['notes'] ?? ''),
                'balance_after' => $currentStock,
            ]);

            StockMovement::create([
                'product_id'   => $data['product_id'],
                'quantity'     => $data['quantity'],
                'type'         => 'transfer_in',
                'branch_id'    => $data['to_branch_id'],
                'user_id'      => auth()->id(),
                'notes'        => "Recebido da filial #{$data['from_branch_id']}. " . ($data['notes'] ?? ''),
                'balance_after' => $currentStock,
            ]);

            return redirect()->route('stock.movements')
                ->with('success', 'Transferência realizada com sucesso.');
        }

        $currentStock = $product->stock;

        if (in_array($data['type'], ['entry', 'return'])) {
            $balanceAfter = $currentStock + $data['quantity'];
        } else {
            $qty = min($data['quantity'], $currentStock);
            $balanceAfter = $currentStock - $qty;
        }

        $movement = StockMovement::create([
            'product_id'   => $data['product_id'],
            'type'         => $data['type'],
            'quantity'     => $data['quantity'],
            'branch_id'    => $data['branch_id'],
            'batch_number' => $data['batch_number'] ?? null,
            'expiry_date'  => $data['expiry_date'] ?? null,
            'user_id'      => auth()->id(),
            'notes'        => $data['notes'] ?? null,
            'balance_after' => $balanceAfter,
        ]);

        if (in_array($data['type'], ['entry', 'return'])) {
            $product->increment('stock', $data['quantity']);
        } elseif (in_array($data['type'], ['exit', 'loss', 'adjustment'])) {
            $product->decrement('stock', $qty);
            if ($qty !== (float) $data['quantity']) {
                $movement->update(['quantity' => $qty, 'balance_after' => $balanceAfter]);
            }
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

    public function transfer(Request $request, NfeService $nfeService)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.01',
            'from_branch_id' => 'required|exists:branches,id',
            'to_branch_id' => 'required|exists:branches,id|different:from_branch_id',
            'notes' => 'nullable|string',
            'emitir_nfe' => 'nullable|boolean',
        ]);

        $product = Product::findOrFail($data['product_id']);
        $fromBranch = Branch::findOrFail($data['from_branch_id']);
        $toBranch = Branch::findOrFail($data['to_branch_id']);
        $currentStock = $product->stock;

        StockMovement::create([
            'product_id'   => $data['product_id'],
            'quantity'     => $data['quantity'],
            'type'         => 'transfer_out',
            'branch_id'    => $data['from_branch_id'],
            'user_id'      => auth()->id(),
            'notes'        => "Transferido para filial #{$data['to_branch_id']}. " . ($data['notes'] ?? ''),
            'balance_after' => $currentStock,
        ]);

        StockMovement::create([
            'product_id'   => $data['product_id'],
            'quantity'     => $data['quantity'],
            'type'         => 'transfer_in',
            'branch_id'    => $data['to_branch_id'],
            'user_id'      => auth()->id(),
            'notes'        => "Recebido da filial #{$data['from_branch_id']}. " . ($data['notes'] ?? ''),
            'balance_after' => $currentStock,
        ]);

        if (!empty($data['emitir_nfe'])) {
            $result = $nfeService->emitirTransferencia(
                $product,
                $fromBranch,
                $toBranch,
                (float) $data['quantity'],
                auth()->user(),
            );

            if (!$result->success) {
                return redirect()->route('stock.movements')
                    ->with('warning', "Transferência realizada, mas falha ao emitir NF-e: {$result->errorMessage}");
            }
        }

        return redirect()->route('stock.movements')
            ->with('success', 'Transferência realizada com sucesso.');
    }

    public function reorderSuggestions(StockForecastService $service)
    {
        $suggestions = $service->suggestPurchaseOrder();
        return view('stock.reorder-suggestions', compact('suggestions'));
    }

    public function expiring(StockForecastService $service, Request $request)
    {
        $days = $request->integer('days', 30);
        $products = $service->expiringProducts($days);
        return view('stock.expiring', compact('products', 'days'));
    }

    public function dashboard(StockForecastService $service)
    {
        $totalProducts = Product::where('is_active', true)->count();
        $lowStock = Product::whereColumn('stock', '<=', 'min_stock')->where('is_active', true)->count();
        $belowReorder = Product::needingReorder()->count();
        $expiringSoon = Product::expiringSoon(30)->count();
        $expired = Product::expired()->count();
        $totalStockValue = Product::where('is_active', true)->get()->sum(fn ($p) => $p->stock * $p->cost_price);

        return view('stock.index', compact(
            'totalProducts', 'lowStock', 'belowReorder',
            'expiringSoon', 'expired', 'totalStockValue'
        ));
    }
}
