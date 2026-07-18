<?php

namespace App\Http\Controllers;

use App\Models\NfeInvoice;
use Illuminate\Http\Request;

class NfceController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:nfe.view');
    }

    public function index(Request $request)
    {
        $query = NfeInvoice::where('tipo', 'nfce')->with(['branch', 'invoice']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $nfceInvoices = $query->latest()->get();

        return view('nfce.index', compact('nfceInvoices'));
    }

    public function show(NfeInvoice $nfceInvoice)
    {
        $nfceInvoice->load(['branch', 'invoice.tutor']);
        return view('nfce.show', compact('nfceInvoice'));
    }
}
