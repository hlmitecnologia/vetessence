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
        $gateways = PaymentGateway::withoutBranch()->orderBy('name')->get();
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
            'channel' => 'required|in:portal,pdv,both',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'public_key' => 'nullable|string|required_if:provider,pix',
            'secret_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'config' => 'nullable',
            'config.url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox');

        if (empty($validated['branch_id'])) {
            $validated['branch_id'] = null;
        }

        if ($request->has('config') && is_array($request->config)) {
            $validated['config'] = array_merge([
                'url' => '',
            ], $request->config);
        } elseif ($request->config) {
            $validated['config'] = json_decode($request->config, true);
        } else {
            $validated['config'] = null;
        }

        if ($validated['is_active']) {
            $this->deactivateOtherGateways($validated['channel']);
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
            'channel' => 'required|in:portal,pdv,both',
            'is_active' => 'boolean',
            'is_sandbox' => 'boolean',
            'public_key' => 'nullable|string|required_if:provider,pix',
            'secret_key' => 'nullable|string',
            'webhook_secret' => 'nullable|string',
            'config' => 'nullable',
            'config.url' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox');

        if (empty($validated['branch_id'])) {
            $validated['branch_id'] = null;
        }

        if ($request->has('config') && is_array($request->config)) {
            $validated['config'] = array_merge([
                'url' => '',
            ], $request->config);
        } elseif ($request->config) {
            $validated['config'] = json_decode($request->config, true);
        } else {
            $validated['config'] = null;
        }

        if ($validated['is_active']) {
            $this->deactivateOtherGateways($validated['channel'], $paymentGateway->id);
        }

        $paymentGateway->update($validated);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Gateway atualizado com sucesso!');
    }

    protected function deactivateOtherGateways(string $channel, ?int $exceptId = null)
    {
        $query = PaymentGateway::withoutBranch()->where('is_active', true);

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        if ($channel === 'portal') {
            $query->whereIn('channel', ['portal', 'both']);
        } elseif ($channel === 'pdv') {
            $query->whereIn('channel', ['pdv', 'both']);
        }

        $query->update(['is_active' => false]);
    }

    public function destroy(PaymentGateway $paymentGateway)
    {
        if ($paymentGateway->invoices()->exists()) {
            return back()->with('error', 'Não é possível excluir gateway com pagamentos registrados.');
        }

        $paymentGateway->delete();
        return redirect()->route('payment-gateways.index')->with('success', 'Gateway excluído.');
    }
}
