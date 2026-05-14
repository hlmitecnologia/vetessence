<?php

namespace App\Http\Controllers;

use App\Models\PaymentGateway;
use Illuminate\Http\Request;

class PaymentGatewayController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:gateway-pagamento');
    }
    public function index()
    {
        $gateways = PaymentGateway::orderBy('name')->paginate(20);
        return view('payment-gateways.index', compact('gateways'));
    }

    public function create()
    {
        return view('payment-gateways.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:50',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'config' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox', true);
        $validated['config'] = $request->config ? json_decode($request->config, true) : null;

        if ($validated['is_active']) {
            PaymentGateway::where('is_active', true)->update(['is_active' => false]);
        }

        PaymentGateway::create($validated);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Gateway cadastrado com sucesso!');
    }

    public function show(PaymentGateway $paymentGateway)
    {
        return view('payment-gateways.show', compact('paymentGateway'));
    }

    public function edit(PaymentGateway $paymentGateway)
    {
        return view('payment-gateways.edit', compact('paymentGateway'));
    }

    public function update(Request $request, PaymentGateway $paymentGateway)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'provider' => 'required|string|max:50',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'public_key' => 'nullable|string',
            'secret_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'config' => 'nullable|json',
            'notes' => 'nullable|string',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox', true);
        $validated['config'] = $request->config ? json_decode($request->config, true) : null;

        if ($validated['is_active']) {
            PaymentGateway::where('is_active', true)->where('id', '!=', $paymentGateway->id)->update(['is_active' => false]);
        }

        $paymentGateway->update($validated);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Gateway atualizado com sucesso!');
    }

    public function destroy(PaymentGateway $paymentGateway)
    {
        $paymentGateway->delete();
        return redirect()->route('payment-gateways.index')->with('success', 'Gateway excluído.');
    }
}
