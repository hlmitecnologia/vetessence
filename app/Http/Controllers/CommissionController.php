<?php

namespace App\Http\Controllers;

use App\Models\CommissionLog;
use App\Models\CommissionRate;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:commissions.view')->only(['index', 'show']);
        $this->middleware('can:commissions.create')->only(['create', 'store']);
        $this->middleware('can:commissions.edit')->only(['edit', 'update']);
        $this->middleware('can:commissions.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = CommissionLog::with(['user', 'invoice']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $logs = $query->orderBy('created_at', 'desc')->paginate(20);
        $vets = User::whereHas('roles', fn($q) => $q->whereIn('name', ['veterinarian', 'super-admin']))->orderBy('name')->get();

        $totals = (clone $query)->selectRaw('SUM(base_value) as total_base, SUM(commission_value) as total_commission')->first();

        return view('commissions.index', compact('logs', 'vets', 'totals'));
    }

    public function show(CommissionLog $commissionLog)
    {
        $commissionLog->load(['user', 'invoice.items', 'commissionRate']);
        return view('commissions.show', compact('commissionLog'));
    }

    public function markPaid(CommissionLog $commissionLog)
    {
        $commissionLog->update(['status' => 'paid', 'paid_at' => now()]);
        return back()->with('success', 'Comissão marcada como paga.');
    }

    public function rates()
    {
        $rates = CommissionRate::with(['user', 'commissionable'])->orderBy('user_id')->paginate(20);
        $vets = User::whereHas('roles', fn($q) => $q->whereIn('name', ['veterinarian', 'super-admin']))->orderBy('name')->get();
        $services = Service::orderBy('name')->get();
        $products = Product::orderBy('name')->get();

        return view('commissions.rates', compact('rates', 'vets', 'services', 'products'));
    }

    public function ratesStore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'commissionable_type' => 'required|in:service,product',
            'commissionable_id' => 'required|integer',
            'rate_type' => 'required|in:percentage,fixed',
            'rate_value' => 'required|numeric|min:0',
        ]);

        $validated['commissionable_type'] = $validated['commissionable_type'] === 'service'
            ? Service::class
            : Product::class;

        CommissionRate::create($validated);

        return redirect()->route('commissions.rates')->with('success', 'Taxa de comissão cadastrada.');
    }

    public function ratesDestroy(CommissionRate $commissionRate)
    {
        $commissionRate->delete();
        return back()->with('success', 'Taxa de comissão excluída.');
    }
}
