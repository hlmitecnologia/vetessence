<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use App\Models\BankTransaction;
use App\Models\Invoice;
use Illuminate\Http\Request;

class BankReconciliationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:bank-reconciliation.view');
    }

    public function index(Request $request)
    {
        $query = BankTransaction::with(['bankAccount.branch', 'invoice']);

        if ($request->bank_account_id) {
            $query->where('bank_account_id', $request->bank_account_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);
        $accounts = BankAccount::where('is_active', true)->orderBy('bank')->get();

        return view('bank-reconciliation.index', compact('transactions', 'accounts'));
    }

    public function match(Request $request, BankTransaction $bankTransaction)
    {
        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $bankTransaction->update([
            'invoice_id' => $validated['invoice_id'],
            'status' => 'reconciled',
        ]);

        return back()->with('success', 'Transação reconciliada com fatura.');
    }

    public function unmatch(BankTransaction $bankTransaction)
    {
        $bankTransaction->update([
            'invoice_id' => null,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Reconciliação desfeita.');
    }

    public function suggest(BankAccount $bankAccount)
    {
        $transactions = BankTransaction::where('bank_account_id', $bankAccount->id)
            ->where('status', 'pending')
            ->where('type', 'credit')
            ->get();

        $invoices = Invoice::whereIn('status', ['paid', 'pending'])
            ->where('total', '>', 0)
            ->orderBy('due_date', 'desc')
            ->get();

        $suggestions = [];

        foreach ($transactions as $tx) {
            $match = $invoices->first(fn($inv) => abs((float) $inv->total - (float) $tx->amount) < 0.01);

            if ($match) {
                $suggestions[] = [
                    'transaction' => $tx,
                    'invoice' => $match,
                ];
            }
        }

        return view('bank-reconciliation.suggestions', compact('suggestions', 'bankAccount'));
    }
}
