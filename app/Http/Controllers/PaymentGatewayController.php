<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\PaymentGateway;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

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
            'secret_key' => 'nullable|string|required_if:provider,multicard,mercadopago',
            'webhook_secret' => 'nullable|string',
            'config' => 'nullable',
            'config.url' => 'nullable|string|max:255',
            'config.pinpdv_id' => 'nullable|required_if:provider,multicard|integer',
            'config.terminal_id' => 'nullable|integer',
            'config.ambiente' => 'nullable|required_if:provider,multicard|in:homologacao,producao',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox');

        if (empty($validated['branch_id'])) {
            $validated['branch_id'] = null;
        }

        $config = $request->has('config') && is_array($request->config) ? $request->config : [];
        $validated['config'] = array_filter([
            'url' => $config['url'] ?? '',
            'pinpdv_id' => $config['pinpdv_id'] ?? null,
            'terminal_id' => $config['terminal_id'] ?? null,
            'ambiente' => $config['ambiente'] ?? null,
        ], fn ($v) => $v !== '' && $v !== null);

        if (empty($validated['config'])) {
            $validated['config'] = null;
        }

        if ($validated['is_active']) {
            $conflict = $this->findConflictingActiveGateway($validated['channel'], $validated['provider']);
            if ($conflict) {
                return back()->withInput()->with('error',
                    'Para ativar este gateway, você precisa desativar primeiro o gateway "' . $conflict->name . '".'
                );
            }
        }

        $gateway = PaymentGateway::create($validated);

        if ($validated['provider'] === 'mercadopago' && !empty($validated['secret_key'])) {
            return redirect()->route('payment-gateways.edit', $gateway)
                ->with('success', 'Gateway cadastrado! Agora selecione o Terminal ID (Point Smart).');
        }

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
            'secret_key' => 'nullable|string|required_if:provider,multicard,mercadopago',
            'webhook_secret' => 'nullable|string',
            'config' => 'nullable',
            'config.url' => 'nullable|string|max:255',
            'config.pinpdv_id' => 'nullable|required_if:provider,multicard|integer',
            'config.terminal_id' => 'nullable|integer',
            'config.ambiente' => 'nullable|required_if:provider,multicard|in:homologacao,producao',
            'notes' => 'nullable|string',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active', false);
        $validated['is_sandbox'] = $request->boolean('is_sandbox');

        if (empty($validated['branch_id'])) {
            $validated['branch_id'] = null;
        }

        $config = $request->has('config') && is_array($request->config) ? $request->config : [];
        $validated['config'] = array_filter([
            'url' => $config['url'] ?? '',
            'pinpdv_id' => $config['pinpdv_id'] ?? null,
            'terminal_id' => $config['terminal_id'] ?? null,
            'ambiente' => $config['ambiente'] ?? null,
        ], fn ($v) => $v !== '' && $v !== null);

        if (empty($validated['config'])) {
            $validated['config'] = null;
        }

        if ($validated['is_active']) {
            $conflict = $this->findConflictingActiveGateway($validated['channel'], $validated['provider'], $paymentGateway->id);
            if ($conflict) {
                return back()->withInput()->with('error',
                    'Para ativar este gateway, você precisa desativar primeiro o gateway "' . $conflict->name . '".'
                );
            }
        }

        $paymentGateway->update($validated);

        return redirect()->route('payment-gateways.index')
            ->with('success', 'Gateway atualizado com sucesso!');
    }

    public function listMpDevices(PaymentGateway $paymentGateway)
    {
        if ($paymentGateway->provider !== 'mercadopago' || empty($paymentGateway->secret_key)) {
            return response()->json(['success' => false, 'message' => 'Gateway não é Mercado Pago ou access token não configurado.'], 422);
        }

        try {
            $response = Http::withToken($paymentGateway->secret_key)
                ->timeout(10)
                ->get('https://api.mercadopago.com/point/integration-api/devices');

            if ($response->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro ao consultar devices: ' . $response->body(),
                ], $response->status());
            }

            $devices = $response->json('devices', []);
            $list = collect($devices)->map(fn ($d) => [
                'id' => $d['id'] ?? null,
                'name' => ($d['name'] ?? '') . ' (' . ($d['type'] ?? '') . ')',
                'status' => $d['status'] ?? 'unknown',
            ])->values();

            return response()->json(['success' => true, 'devices' => $list]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de conexão: ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function findConflictingActiveGateway(string $channel, string $provider, ?int $exceptId = null): ?PaymentGateway
    {
        $conflictingChannels = match ($channel) {
            'portal' => ['portal', 'both'],
            'pdv' => ['pdv', 'both'],
            'both' => ['portal', 'pdv', 'both'],
            default => [],
        };

        $query = PaymentGateway::withoutBranch()
            ->where('is_active', true)
            ->where(function ($q) use ($conflictingChannels, $provider) {
                $q->whereIn('channel', $conflictingChannels);

                if ($provider === 'pix') {
                    $q->orWhere('provider', 'pix');
                }
            });

        if ($exceptId) {
            $query->where('id', '!=', $exceptId);
        }

        return $query->first();
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
