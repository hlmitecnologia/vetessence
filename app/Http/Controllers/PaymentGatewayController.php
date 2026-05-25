<?php

namespace App\Http\Controllers;

use App\Models\Branch;
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
        $branches = Branch::orderBy('name')->get();
        return view('payment-gateways.create', compact('branches'));
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
            'config.city' => 'nullable|string|max:50',
            'config.gi' => 'nullable|string|max:50',
            'config.url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox', true);

        if ($request->has('config') && is_array($request->config)) {
            $validated['config'] = array_merge([
                'city' => '',
                'gi' => 'br.gov.bcb.pix',
                'url' => '',
            ], $request->config);
        } elseif ($request->config) {
            $validated['config'] = json_decode($request->config, true);
        } else {
            $validated['config'] = null;
        }

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
        $branches = Branch::orderBy('name')->get();
        return view('payment-gateways.edit', compact('paymentGateway', 'branches'));
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
            'config.city' => 'nullable|string|max:50',
            'config.gi' => 'nullable|string|max:50',
            'config.url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox', true);

        if ($request->has('config') && is_array($request->config)) {
            $validated['config'] = array_merge([
                'city' => '',
                'gi' => 'br.gov.bcb.pix',
                'url' => '',
            ], $request->config);
        } elseif ($request->config) {
            $validated['config'] = json_decode($request->config, true);
        } else {
            $validated['config'] = null;
        }

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
