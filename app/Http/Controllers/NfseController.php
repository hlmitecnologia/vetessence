<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\NfseInvoice;
use App\Services\Nfse\NfseService;
use Illuminate\Http\Request;

class NfseController extends Controller
{
    public function __construct(
        protected NfseService $nfseService,
    ) {}

    public function index(Request $request)
    {
        $query = NfseInvoice::with(['branch', 'invoice']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $nfseInvoices = $query->latest()->paginate(20);

        return view('nfse.index', compact('nfseInvoices'));
    }

    public function show(NfseInvoice $nfseInvoice)
    {
        $nfseInvoice->load(['branch', 'invoice.tutor']);
        return view('nfse.show', compact('nfseInvoice'));
    }

    public function emitir(Invoice $invoice)
    {
        $result = $this->nfseService->emitir($invoice);

        if (!$result->success) {
            return back()->with('error', $result->errorMessage);
        }

        return back()->with('success', "NFSe emitida! Nº {$result->nfseNumber}");
    }

    public function cancelar(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $result = $this->nfseService->cancelar($invoice, $validated['motivo']);

        if (!$result->success) {
            return back()->with('error', $result->errorMessage);
        }

        return back()->with('success', 'NFSe cancelada com sucesso.');
    }

    public function downloadXml(NfseInvoice $nfseInvoice)
    {
        if (!$nfseInvoice->nfse_url_xml) {
            return back()->with('error', 'XML não disponível.');
        }

        return redirect($nfseInvoice->nfse_url_xml);
    }

    public function downloadPdf(NfseInvoice $nfseInvoice)
    {
        if (!$nfseInvoice->nfse_url_pdf) {
            return back()->with('error', 'PDF não disponível.');
        }

        return redirect($nfseInvoice->nfse_url_pdf);
    }
}
