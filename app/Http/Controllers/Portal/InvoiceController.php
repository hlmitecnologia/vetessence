<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Services\PaymentService;
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

        $hasPortalGateway = PaymentGateway::withoutBranch()->active()
            ->whereIn('channel', ['portal', 'both'])
            ->whereNull('branch_id')
            ->exists();

        if (!$hasPortalGateway && $invoice->branch_id) {
            $hasPortalGateway = PaymentGateway::withoutBranch()->active()
                ->whereIn('channel', ['portal', 'both'])
                ->where('branch_id', $invoice->branch_id)
                ->exists();
        }

        return view('portal.invoices.show', compact('invoice', 'hasPortalGateway'));
    }

    public function checkout($id, PaymentService $paymentService)
    {
        $invoice = Auth::guard('tutor')->user()->invoices()->findOrFail($id);

        if ($invoice->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Fatura já foi paga.'], 400);
        }

        $result = $paymentService->checkout($invoice);

        if ($result['success'] && $result['redirect_url']) {
            return response()->json($result);
        }

        if ($result['success'] && !$result['redirect_url']) {
            $invoice->refresh();
            $result['pix_code'] = view('portal.invoices._pix', compact('invoice'))->render();
            return response()->json($result);
        }

        return response()->json($result, 422);
    }

    public function download($id)
    {
        $invoice = Auth::guard('tutor')->user()->invoices()->with('items')->findOrFail($id);

        $filename = 'fatura-' . ($invoice->invoice_number ?? $invoice->id) . '.pdf';

        $pdf = Pdf::loadView('portal.invoices.pdf', compact('invoice'));

        return $pdf->download($filename);
    }
}
