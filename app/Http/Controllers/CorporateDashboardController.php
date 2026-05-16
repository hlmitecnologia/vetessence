<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CorporateDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:corporate-dashboard.view');
    }

    public function index(Request $request)
    {
        $branches = Branch::all();
        $branchId = $request->branch_id;

        $stats = [];
        $branchStats = [];

        foreach ($branches as $branch) {
            if ($branchId && $branch->id != $branchId) continue;

            $appQuery = Appointment::when($branchId, fn($q) => $q->where('branch_id', $branchId));
            $invQuery = Invoice::when($branchId, fn($q) => $q->where('branch_id', $branchId));
            $petQuery = Pet::when($branchId, fn($q) => $q->where('branch_id', $branchId));

            $stats['total_appointments'] = ($stats['total_appointments'] ?? 0) + $appQuery->count();
            $stats['total_invoiced'] = ($stats['total_invoiced'] ?? 0) + $invQuery->sum('total');
            $stats['total_pets'] = ($stats['total_pets'] ?? 0) + $petQuery->count();
            $stats['today_appointments'] = ($stats['today_appointments'] ?? 0) + $appQuery->whereDate('date', today())->count();

            $monthly = $invQuery
                ->select(DB::raw('MONTH(created_at) as month'), DB::raw('SUM(total) as total'))
                ->whereYear('created_at', now()->year)
                ->groupBy('month')
                ->pluck('total', 'month');

            $patients = $appQuery
                ->select(DB::raw('COUNT(DISTINCT pet_id) as total'))
                ->whereMonth('date', now()->month)
                ->value('total');

            $branchStats[] = (object)[
                'branch' => $branch,
                'appointments' => $appQuery->count(),
                'invoiced' => $invQuery->sum('total'),
                'pets' => $petQuery->count(),
                'monthly' => $monthly,
                'patients' => $patients,
            ];
        }

        $users = User::count();

        return view('corporate-dashboard.index', compact(
            'branches', 'branchId', 'stats', 'branchStats', 'users'
        ));
    }
}
