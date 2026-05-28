<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Hospitalization;
use App\Models\Invoice;
use App\Models\MedicalRecord;
use App\Models\Pet;
use App\Models\Product;
use App\Models\VaccinationReminder;
use App\Models\ParasiteControl;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Stats
        $stats = [
            'todayAppointments' => Appointment::where('date', today())
                ->whereIn('status', ['scheduled', 'confirmed', 'in_progress'])
                ->count(),
            'totalPets' => Pet::where('is_active', true)->count(),
            'monthRevenue' => Invoice::whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->where('status', 'paid')
                ->sum('total'),
            'lowStock' => Product::whereColumn('stock', '<=', 'min_stock')
                ->where('is_active', true)
                ->count(),
            'todayRevenue' => Invoice::whereDate('paid_at', today())
                ->where('status', 'paid')
                ->sum('total'),
            'todayProcedures' => MedicalRecord::whereDate('created_at', today())->count(),
            'activeHospitalizations' => Hospitalization::where('status', 'admitted')->count(),
            'noShowRate' => $this->computeNoShowRate(),
            'pendingReminders' => VaccinationReminder::where('status', 'pending')
                ->where('scheduled_date', '<=', today())
                ->count(),
            'overdueParasiteControls' => ParasiteControl::whereNotNull('next_due_date')
                ->where('next_due_date', '<', today())
                ->count(),
        ];

        // Lists
        $upcomingAppointments = Appointment::with('pet.tutors')
            ->where('date', '>=', today())
            ->whereIn('status', ['scheduled', 'confirmed'])
            ->orderBy('date')
            ->orderBy('time')
            ->limit(5)
            ->get();

        $recentRecords = MedicalRecord::with(['pet', 'vet'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $lowStockProducts = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('is_active', true)
            ->limit(5)
            ->get();

        // Charts Data
        // Revenue by month (last 6 months)
        $revenueByMonth = Invoice::select(
                DB::raw("DATE_FORMAT(paid_at, '%Y-%m') as month"),
                DB::raw('SUM(total) as total')
            )
            ->where('status', 'paid')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->groupByRaw("DATE_FORMAT(paid_at, '%Y-%m')")
            ->orderByRaw("DATE_FORMAT(paid_at, '%Y-%m')")
            ->get();

        // Appointments by type (current month)
        $appointmentsByType = Appointment::select('type', DB::raw('COUNT(*) as count'))
            ->whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->groupBy('type')
            ->get();

        // Species distribution
        $speciesDistribution = Pet::select('species', DB::raw('COUNT(*) as count'))
            ->where('is_active', true)
            ->groupBy('species')
            ->get()
            ->map(function ($item) {
                $labels = [
                    'canine' => 'Caninos',
                    'feline' => 'Felinos',
                    'avian' => 'Aves',
                    'exotic' => 'Exóticos',
                    'reptile' => 'Répteis',
                    'small_mammal' => 'Pequenos Mamíferos'
                ];
                return ['name' => $labels[$item->species] ?? $item->species, 'count' => $item->count];
            });

        // Overdue reminders list
        $overdueReminders = VaccinationReminder::with(['pet', 'vaccination'])
            ->where('status', 'pending')
            ->where('scheduled_date', '<=', today())
            ->orderBy('scheduled_date')
            ->limit(5)
            ->get();

        $overdueParasiteList = ParasiteControl::with('pet')
            ->whereNotNull('next_due_date')
            ->where('next_due_date', '<', today())
            ->orderBy('next_due_date')
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'upcomingAppointments',
            'recentRecords',
            'lowStockProducts',
            'revenueByMonth',
            'appointmentsByType',
            'speciesDistribution',
            'overdueReminders',
            'overdueParasiteList'
        ));
    }

    protected function computeNoShowRate(): float
    {
        $total = Appointment::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->count();
        if ($total === 0) return 0;
        $noShow = Appointment::whereMonth('date', now()->month)
            ->whereYear('date', now()->year)
            ->where('status', 'no_show')
            ->count();
        return round(($noShow / $total) * 100, 1);
    }
}
