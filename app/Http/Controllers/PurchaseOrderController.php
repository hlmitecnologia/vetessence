<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class PurchaseOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:purchase-orders.view')->only(['index', 'show']);
        $this->middleware('can:purchase-orders.create')->only(['create', 'store']);
        $this->middleware('can:purchase-orders.edit')->only(['edit', 'update']);
        $this->middleware('can:purchase-orders.delete')->only(['destroy']);
        $this->middleware('can:purchase-orders.approve')->only(['order']);
        $this->middleware('can:purchase-orders.receive')->only(['receive']);
    }

    public function index()
    {
        $orders = PurchaseOrder::with(['supplier', 'requester'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('purchase-orders.index', compact('orders'));
    }

    public function create()
    {
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);

        $order = PurchaseOrder::create([
            'order_number' => PurchaseOrder::generateNumber(),
            'supplier_id' => $data['supplier_id'],
            'requested_by' => auth()->id(),
            'status' => 'draft',
            'total' => $total,
            'notes' => $data['notes'] ?? null,
        ]);

        foreach ($data['items'] as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return redirect()->route('purchase-orders.show', $order)
            ->with('success', 'Pedido de compra criado.');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'requester', 'approver', 'items.product']);
        return view('purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Apenas pedidos em rascunho podem ser editados.');
        }
        $suppliers = Supplier::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Apenas pedidos em rascunho podem ser editados.');
        }

        $data = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $total = collect($data['items'])->sum(fn($i) => $i['quantity'] * $i['unit_price']);

        $purchaseOrder->update([
            'supplier_id' => $data['supplier_id'],
            'total' => $total,
            'notes' => $data['notes'] ?? null,
        ]);

        $purchaseOrder->items()->delete();
        foreach ($data['items'] as $item) {
            PurchaseOrderItem::create([
                'purchase_order_id' => $purchaseOrder->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ]);
        }

        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Pedido de compra atualizado.');
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Apenas pedidos em rascunho podem ser excluídos.');
        }
        $purchaseOrder->items()->delete();
        $purchaseOrder->delete();
        return redirect()->route('purchase-orders.index')
            ->with('success', 'Pedido de compra excluído.');
    }

    public function order(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'draft') {
            return back()->with('error', 'Pedido já foi enviado.');
        }
        $purchaseOrder->update(['status' => 'ordered', 'ordered_at' => now()]);
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Pedido enviado ao fornecedor.');
    }

    public function receive(PurchaseOrder $purchaseOrder, Request $request)
    {
        if ($purchaseOrder->status !== 'ordered') {
            return back()->with('error', 'Pedido precisa estar como "enviado" para receber.');
        }

        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:purchase_order_items,id',
            'items.*.received_quantity' => 'required|numeric|min:0',
        ]);

        foreach ($data['items'] as $item) {
            PurchaseOrderItem::find($item['id'])->update([
                'received_quantity' => $item['received_quantity'],
            ]);
        }

        $purchaseOrder->update(['status' => 'received', 'received_at' => now()]);
        return redirect()->route('purchase-orders.show', $purchaseOrder)
            ->with('success', 'Pedido recebido com sucesso.');
    }
}
