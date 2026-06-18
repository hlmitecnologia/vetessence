<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\NfseInvoice;
use App\Services\Nfse\NfseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class NfseController extends Controller
{
    public function __construct(
        protected NfseService $nfseService,
    ) {
        $this->middleware('can:nfse.view');
    }

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

    public function exportForm()
    {
        return view('nfse.export');
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $query = NfseInvoice::where('status', 'issued')->whereNotNull('nfse_url_xml');

        $query->whereDate('issuance_date', '>=', $validated['date_from']);
        $query->whereDate('issuance_date', '<=', $validated['date_to']);

        if ($branchId = $validated['branch_id'] ?? null) {
            $query->where('branch_id', $branchId);
        }

        $nfseInvoices = $query->get();

        if ($nfseInvoices->isEmpty()) {
            return back()->with('error', 'Nenhuma NFSe encontrada para exportar.');
        }

        $exportDir = 'nfse/exports';
        Storage::makeDirectory($exportDir);

        $zipPath = storage_path("app/{$exportDir}/nfse-export-" . now()->format('YmdHis') . '.zip');
        $zip = new ZipArchive;

        if ($zip->open($zipPath, ZipArchive::CREATE) !== true) {
            return back()->with('error', 'Falha ao criar arquivo ZIP.');
        }

        $downloaded = 0;

        foreach ($nfseInvoices as $nfse) {
            try {
                $response = Http::timeout(30)->get($nfse->nfse_url_xml);

                if ($response->successful()) {
                    $filename = "nfse-{$nfse->nfse_number}-{$nfse->branch_id}.xml";
                    $zip->addFromString($filename, $response->body());
                    $downloaded++;
                }
            } catch (\Exception $e) {
                //
            }
        }

        $zip->close();

        if ($downloaded === 0) {
            unlink($zipPath);
            return back()->with('error', 'Falha ao baixar os XMLs.');
        }

        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
}
