<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:bank-reconciliation.view')->only(['index', 'show']);
        $this->middleware('can:bank-reconciliation.create')->only(['create', 'store']);
        $this->middleware('can:bank-reconciliation.edit')->only(['edit', 'update']);
        $this->middleware('can:bank-reconciliation.delete')->only(['destroy']);
    }

    public function index()
    {
        $accounts = BankAccount::with('branch')->orderBy('bank')->paginate(20);
        return view('bank-accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('bank-accounts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'bank' => 'required|string|max:100',
            'agency' => 'required|string|max:20',
            'account' => 'required|string|max:20',
            'account_type' => 'required|in:checking,savings',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        BankAccount::create($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária cadastrada.');
    }

    public function show(BankAccount $bankAccount)
    {
        $bankAccount->load(['branch', 'transactions' => fn($q) => $q->latest()]);
        return view('bank-accounts.show', compact('bankAccount'));
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('bank-accounts.edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $validated = $request->validate([
            'bank' => 'required|string|max:100',
            'agency' => 'required|string|max:20',
            'account' => 'required|string|max:20',
            'account_type' => 'required|in:checking,savings',
            'description' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária atualizada.');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return redirect()->route('bank-accounts.index')->with('success', 'Conta bancária excluída.');
    }
}
