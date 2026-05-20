<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\NfseConfig;
use App\Models\Pet;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['tutor', 'pet', 'nfseInvoice']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        $hasNfseConfig = NfseConfig::where('branch_id', auth()->user()->branch_id)
            ->where('is_active', true)->exists();

        return view('invoices.index', compact('invoices', 'hasNfseConfig'));
    }

    public function create(Request $request)
    {
        $tutors = Tutor::orderBy('name')->get();
        
        $tutorId = $request->tutor_id;
        $tutor = $tutorId ? Tutor::with('primaryPets')->find($tutorId) : null;

        return view('invoices.create', compact('tutors', 'tutor'));
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
                'created_by' => auth()->id(),
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            DB::commit();
            return redirect()->route('invoices.show', $invoice)->with('success', 'Fatura criada com sucesso!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Erro ao criar fatura.')->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['tutor', 'pet', 'items', 'creator', 'nfseInvoice']);

        $hasNfseConfig = NfseConfig::where('branch_id', auth()->user()->branch_id)
            ->where('is_active', true)->exists();

        return view('invoices.show', compact('invoice', 'hasNfseConfig'));
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

        return redirect()->back()->with('success', 'Pagamento registrado!');
    }
}
