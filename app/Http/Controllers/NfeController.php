<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\NfeInvoice;
use App\Services\Nfe\NfeService;
use Illuminate\Http\Request;

class NfeController extends Controller
{
    public function __construct(
        protected NfeService $nfeService,
    ) {
        $this->middleware('can:nfe.view');
    }

    public function index(Request $request)
    {
        $query = NfeInvoice::with(['branch', 'invoice']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $nfeInvoices = $query->latest()->get();

        return view('nfe.index', compact('nfeInvoices'));
    }

    public function show(NfeInvoice $nfeInvoice)
    {
        $nfeInvoice->load(['branch', 'invoice.tutor']);
        return view('nfe.show', compact('nfeInvoice'));
    }

    public function emitir(Invoice $invoice)
    {
        $result = $this->nfeService->emitir($invoice);

        if (!$result->success) {
            return back()->with('error', $result->errorMessage);
        }

        return back()->with('success', "NF-e emitida! Nº {$result->nfeNumber}");
    }

    public function cancelar(Request $request, Invoice $invoice)
    {
        $validated = $request->validate([
            'motivo' => 'required|string|max:500',
        ]);

        $result = $this->nfeService->cancelar($invoice, $validated['motivo']);

        if (!$result->success) {
            return back()->with('error', $result->errorMessage);
        }

        return back()->with('success', 'NF-e cancelada com sucesso.');
    }

    public function downloadXml(NfeInvoice $nfeInvoice)
    {
        if (!$nfeInvoice->nfe_url_xml) {
            return back()->with('error', 'XML não disponível.');
        }

        return redirect($nfeInvoice->nfe_url_xml);
    }

    public function downloadPdf(NfeInvoice $nfeInvoice)
    {
        if (!$nfeInvoice->nfe_url_pdf) {
            return back()->with('error', 'PDF não disponível.');
        }

        return redirect($nfeInvoice->nfe_url_pdf);
    }

    public function downloadDanfe(NfeInvoice $nfeInvoice)
    {
        if (!$nfeInvoice->danfe_url) {
            return back()->with('error', 'DANFE não disponível.');
        }

        return redirect($nfeInvoice->danfe_url);
    }

    public function exportForm()
    {
        return view('nfe.export');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $nfeInvoices = NfeInvoice::whereBetween('created_at', [$validated['date_from'], $validated['date_to']])
            ->where('status', 'issued')
            ->with('invoice.tutor')
            ->get();

        return view('nfe.export', compact('nfeInvoices'));
    }
}
