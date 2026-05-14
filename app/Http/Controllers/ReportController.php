<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\Pet;
use App\Models\Vaccination;
use App\Models\Exam;
use App\Models\Surgery;
use App\Models\Product;
use App\Models\ZoonoticDisease;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function financial(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $invoices = Invoice::with(['tutor', 'pet'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();

        $totalRevenue = $invoices->where('status', 'paid')->sum('total');
        $totalPending = $invoices->where('status', 'pending')->sum('total');
        $totalOverdue = $invoices->where('status', 'overdue')->sum('total');
        $totalCancelled = $invoices->where('status', 'cancelled')->sum('total');
        $totalDiscounts = $invoices->sum('discount');

        $revenueByDay = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('DATE(paid_at) as date, SUM(total) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $revenueByPaymentMethod = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->selectRaw('payment_method, SUM(total) as total, COUNT(*) as count')
            ->groupBy('payment_method')
            ->get();

        $monthlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $startDate->year)
            ->selectRaw('MONTH(paid_at) as month, SUM(total) as total')
            ->groupByRaw('MONTH(paid_at)')
            ->orderByRaw('MONTH(paid_at)')
            ->get()
            ->keyBy('month');

        $topClients = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->join('tutors', 'invoices.tutor_id', '=', 'tutors.id')
            ->join('users', 'tutors.user_id', '=', 'users.id')
            ->selectRaw('users.name, SUM(invoices.total) as total, COUNT(*) as count')
            ->groupBy('tutors.id', 'users.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $appointmentStats = Appointment::whereBetween('date', [$startDate, $endDate])
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $serviceStats = Appointment::whereBetween('date', [$startDate, $endDate])
            ->join('appointment_services', 'appointments.id', '=', 'appointment_services.appointment_id')
            ->join('services', 'appointment_services.service_id', '=', 'services.id')
            ->selectRaw('services.name, COUNT(*) as count, SUM(appointment_services.price * appointment_services.quantity) as revenue')
            ->groupBy('services.id', 'services.name')
            ->orderBy('revenue', 'desc')
            ->limit(10)
            ->get();

        $vaccinationStats = Vaccination::whereBetween('date', [$startDate, $endDate])->count();
        $examStats = Exam::whereBetween('requested_date', [$startDate, $endDate])->count();
        $surgeryStats = Surgery::whereBetween('scheduled_date', [$startDate, $endDate])->count();

        $totalPets = Pet::count();
        $activePets = Pet::where('is_active', true)->count();
        $newPets = Pet::whereBetween('created_at', [$startDate, $endDate])->count();

        $speciesBreakdown = Pet::selectRaw('species, COUNT(*) as count')
            ->groupBy('species')
            ->get()
            ->keyBy('species');

        $zoonosisStats = ZoonoticDisease::selectRaw('zoonotic_diseases.name, COUNT(diagnosis_disease.medical_record_id) as total')
            ->join('diagnosis_disease', 'zoonotic_diseases.id', '=', 'diagnosis_disease.zoonotic_disease_id')
            ->join('medical_records', 'diagnosis_disease.medical_record_id', '=', 'medical_records.id')
            ->whereBetween('medical_records.date', [$startDate, $endDate])
            ->groupBy('zoonotic_diseases.id', 'zoonotic_diseases.name')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $notifiableCount = ZoonoticDisease::whereHas('medicalRecords', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('date', [$startDate, $endDate]);
        })->where('is_notifiable', true)->count();

        $lowStockProducts = Product::whereRaw('stock <= min_stock')
            ->selectRaw('name, stock, min_stock')
            ->get();

        $overdueInvoices = Invoice::where('status', 'overdue')
            ->with('tutor')
            ->orderBy('due_date')
            ->limit(10)
            ->get();

        return view('reports.financial', compact(
            'startDate',
            'endDate',
            'totalRevenue',
            'totalPending',
            'totalOverdue',
            'totalCancelled',
            'totalDiscounts',
            'revenueByDay',
            'revenueByPaymentMethod',
            'monthlyRevenue',
            'topClients',
            'appointmentStats',
            'serviceStats',
            'vaccinationStats',
            'examStats',
            'surgeryStats',
            'totalPets',
            'activePets',
            'newPets',
            'speciesBreakdown',
            'zoonosisStats',
            'notifiableCount',
            'lowStockProducts',
            'overdueInvoices'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : Carbon::now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : Carbon::now()->endOfMonth();

        $invoices = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [$startDate, $endDate])
            ->with(['tutor', 'items'])
            ->get();

        return view('reports.export', compact('invoices', 'startDate', 'endDate'));
    }
}
