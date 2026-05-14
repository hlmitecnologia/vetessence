<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
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
}
