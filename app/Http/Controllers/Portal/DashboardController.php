<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();

        $petsCount = $tutor->pets()->count();

        $upcomingAppointmentsList = $tutor->pets()
            ->with(['appointments' => function ($q) {
                $q->where('date', '>=', today())->orderBy('date')->orderBy('time');
            }])
            ->get()
            ->pluck('appointments')
            ->flatten()
            ->sortBy(function ($a) {
                return $a->date.' '.$a->time;
            })
            ->take(5);

        $upcomingAppointments = $upcomingAppointmentsList->count();

        $pendingInvoices = $tutor->invoices()
            ->whereIn('status', ['pending', 'overdue'])
            ->count();

        return view('portal.dashboard.index', compact(
            'petsCount',
            'upcomingAppointments',
            'upcomingAppointmentsList',
            'pendingInvoices'
        ));
    }
}
