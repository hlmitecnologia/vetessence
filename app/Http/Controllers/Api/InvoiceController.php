<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['tutor', 'pet', 'items']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $invoices = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($invoices);
    }

    public function show($id)
    {
        $invoice = Invoice::with(['tutor', 'pet', 'items', 'creator'])->find($id);

        if (!$invoice) {
            return response()->json(['error' => 'Fatura não encontrada'], 404);
        }

        return response()->json($invoice);
    }

    public function myInvoices(Request $request)
    {
        $user = $request->user();
        $tutor = $user->tutor;

        if (!$tutor) {
            return response()->json(['error' => 'Tutor não encontrado'], 404);
        }

        $invoices = Invoice::with(['pet', 'items'])
            ->where('tutor_id', $tutor->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($invoices);
    }
}
