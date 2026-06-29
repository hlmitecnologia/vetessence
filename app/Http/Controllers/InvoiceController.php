<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Tutor;
use App\Models\Service;
use App\Models\Product;
use App\Models\NfseConfig;
use App\Models\NfeConfig;
use App\Models\PaymentGateway;
use App\Events\InvoicePaid;
use App\Services\Nfse\NfseService;
use App\Services\Nfse\NfseResult;
use App\Services\Nfe\NfeService;
use App\Services\Nfe\NfeResult;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:financeiro');
    }

    public function index(Request $request)
    {
        $query = Invoice::with(['tutor', 'pet', 'nfseInvoice', 'nfeInvoice'])
            ->withCount([
                'items as has_services' => fn($q) => $q->where('item_type', 'service'),
                'items as has_products' => fn($q) => $q->where('item_type', 'product'),
            ]);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')->get();

        $hasNfseConfig = NfseConfig::where('is_active', true)->exists();
        $hasNfeConfig = NfeConfig::where('is_active', true)->exists();

        return view('invoices.index', compact('invoices', 'hasNfseConfig', 'hasNfeConfig'));
    }

    public function create(Request $request)
    {
        $tutors = Tutor::orderBy('name')->get();
        $services = Service::where('is_active', true)->orderBy('name')->get();
        $products = Product::where('is_active', true)->where('stock', '>', 0)->orderBy('name')->get();
        
        $tutorId = $request->tutor_id;
        $tutor = $tutorId ? Tutor::with('primaryPets')->find($tutorId) : null;

        return view('invoices.create', compact('tutors', 'services', 'products', 'tutor'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tutor_id' => 'required|exists:tutors,id',
            'pet_id' => 'nullable|exists:pets,id',
            'due_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.item_type' => 'required|string|in:service,product,avulso',
            'items.*.product_id' => 'required_if:items.*.item_type,product|exists:products,id',
            'items.*.service_id' => 'required_if:items.*.item_type,service|exists:services,id',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $discount = $request->discount ?? 0;
            $total = $subtotal - $discount;

            $invoice = Invoice::create([
                'invoice_number' => Invoice::generateNumber(),
                'tutor_id' => $validated['tutor_id'],
                'pet_id' => $validated['pet_id'] ?? null,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'due_date' => $validated['due_date'],
                'status' => 'pending',
                'user_id' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                    'item_type' => $item['item_type'] ?? 'service',
                    'product_id' => $item['product_id'] ?? null,
                    'service_id' => $item['service_id'] ?? null,
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.index')->with('success', 'Fatura criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error('Erro ao criar fatura', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input' => $request->except('_token'),
            ]);
            return back()->with('error', 'Erro ao criar fatura: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['tutor', 'pet', 'items', 'creator', 'nfseInvoice', 'nfeInvoice', 'appointments']);

        $hasNfseConfig = NfseConfig::where('is_active', true)->exists();
        $hasNfeConfig = NfeConfig::where('is_active', true)->exists();
        $hasPdvGateway = PaymentGateway::active()
            ->whereIn('channel', ['pdv', 'both'])
            ->exists();

        return view('invoices.show', compact('invoice', 'hasNfseConfig', 'hasNfeConfig', 'hasPdvGateway'));
    }

    public function emitirNotaFiscal(Invoice $invoice, NfseService $nfseService, NfeService $nfeService)
    {
        $invoice->load('items');
        $user = auth()->user();

        if (!$user->can('nfse.emit') && !$user->can('nfe.emit')) {
            abort(403);
        }

        if ($invoice->status !== 'paid') {
            return back()->with('warning', 'A nota fiscal só pode ser emitida após o pagamento da fatura.');
        }

        $results = [];

        if ($user->can('nfse.emit')) {
            $results['nfse'] = $this->emitirNfseSePossivel($invoice, $nfseService);
        }

        if ($user->can('nfe.emit')) {
            $results['nfe'] = $this->emitirNfeSePossivel($invoice, $nfeService);
        }

        if (empty($results)) {
            return back()->with('info', 'Nenhuma nota fiscal necessária para esta fatura.');
        }

        $messages = [];
        foreach ($results as $type => $result) {
            if ($result->success) {
                $messages[] = strtoupper($type) . ': emitida com sucesso.';
            } else {
                $messages[] = strtoupper($type) . ': ' . ($result->errorMessage ?? 'erro desconhecido');
            }
        }

        $hasError = collect($results)->contains(fn($r) => !$r->success);
        $flashType = $hasError ? 'warning' : 'success';

        return back()->with($flashType, implode(' | ', $messages));
    }

    protected function emitirNfseSePossivel(Invoice $invoice, NfseService $nfseService): object
    {
        $hasServiceItems = $invoice->items->where('item_type', 'service')->isNotEmpty();

        if (!$hasServiceItems) {
            return (object) ['success' => true, 'message' => 'Nenhum serviço para emitir NFSe.'];
        }

        if ($invoice->nfse_status !== 'none') {
            return (object) ['success' => true, 'message' => 'NFSe já emitida anteriormente.'];
        }

        if (!NfseConfig::where('is_active', true)->exists()) {
            return NfseResult::error('NFSe não configurada. Configure o provedor de emissão em NFSe > Configurações.');
        }

        if (!$invoice->branch || !$invoice->branch->municipio_ibge) {
            return NfseResult::error('Dados fiscais da unidade incompletos. Configure o código IBGE no cadastro da unidade.');
        }

        return $nfseService->emitir($invoice);
    }

    protected function emitirNfeSePossivel(Invoice $invoice, NfeService $nfeService): object
    {
        $hasProductItems = $invoice->items->where('item_type', 'product')->isNotEmpty();

        if (!$hasProductItems) {
            return (object) ['success' => true, 'message' => 'Nenhum produto para emitir NF-e.'];
        }

        if ($invoice->nfe_status !== 'none') {
            return (object) ['success' => true, 'message' => 'NF-e já emitida anteriormente.'];
        }

        if (!NfeConfig::where('is_active', true)->exists()) {
            return NfeResult::error('NF-e não configurada. Configure o provedor de emissão em NF-e > Configurações.');
        }

        if (!$invoice->branch || !$invoice->branch->cnpj) {
            return NfeResult::error('Dados fiscais da unidade incompletos. Configure o CNPJ no cadastro da unidade.');
        }

        return $nfeService->emitir($invoice);
    }

    public function generatePix(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return response()->json(['error' => 'Fatura já foi paga.'], 400);
        }

        $qrcode = $invoice->generatePixCode();
        
        return response()->json([
            'qrcode' => $qrcode['qrcode_base64'],
            'payload' => $qrcode['payload'],
            'expiration' => $invoice->pix_expiration,
        ]);
    }

    public function edit(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Não é possível editar fatura paga.');
        }

        $invoice->load('items');
        $tutors = Tutor::orderBy('name')->get();

        return view('invoices.edit', compact('invoice', 'tutors'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Não é possível editar fatura paga.');
        }

        $validated = $request->validate([
            'due_date' => 'required|date',
        ]);

        $invoice->update($validated);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Fatura atualizada!');
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Não é possível excluir fatura paga.');
        }

        $invoice->items()->delete();
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Fatura excluída!');
    }

    public function cancel(Invoice $invoice)
    {
        if ($invoice->status === 'paid') {
            return back()->with('error', 'Não é possível cancelar fatura paga.');
        }

        $invoice->update(['status' => 'cancelled']);

        return redirect()->route('invoices.index')->with('success', 'Fatura cancelada!');
    }

    public function charge(Request $request, Invoice $invoice, PaymentService $paymentService)
    {
        if ($invoice->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Fatura já foi paga.'], 400);
        }

        $result = $paymentService->charge($invoice, 'pdv');

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function pay(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_method' => 'required|in:pix,dinheiro,cartao_credito,cartao_debito,boleto,transferencia,convenio',
        ]);

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_method' => $validated['payment_method'],
        ]);

        InvoicePaid::dispatch($invoice);

        return redirect()->back()->with('success', 'Pagamento registrado!');
    }
}
