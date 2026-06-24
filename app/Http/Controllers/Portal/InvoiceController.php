<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Auth::guard('tutor')->user()->invoices()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('portal.invoices.index', compact('invoices'));
    }

    public function show($id)
    {
        $invoice = Auth::guard('tutor')->user()->invoices()->findOrFail($id);

        if ($invoice->status === 'pending' && !$invoice->pix_code) {
            $invoice->generatePixCode();
            $invoice->refresh();
        }

        return view('portal.invoices.show', compact('invoice'));
    }

    public function download($id)
    {
        $invoice = Auth::guard('tutor')->user()->invoices()->with('items')->findOrFail($id);

        $filename = 'fatura-' . ($invoice->invoice_number ?? $invoice->id) . '.pdf';

        $pdf = Pdf::loadView('portal.invoices.pdf', compact('invoice'));

        return $pdf->download($filename);
    }
}
