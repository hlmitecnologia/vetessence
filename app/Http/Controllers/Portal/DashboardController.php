<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vaccination;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        $petIds = $tutor->pets()->pluck('pets.id');

        $petsCount = $petIds->count();

        $upcomingAppointmentsList = $tutor->pets()
            ->with(['appointments' => function ($q) {
                $q->with(['vet', 'branch'])
                  ->where('date', '>=', today())
                  ->orderBy('date')->orderBy('time');
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

        $upcomingVaccinations = Vaccination::with('pet')
            ->whereIn('pet_id', $petIds)
            ->whereNotNull('next_date')
            ->where('next_date', '>=', today())
            ->orderBy('next_date')
            ->take(10)
            ->get();

        $upcomingVaccinationsCount = $upcomingVaccinations->count();

        return view('portal.dashboard.index', compact(
            'petsCount',
            'upcomingAppointments',
            'upcomingAppointmentsList',
            'pendingInvoices',
            'upcomingVaccinations',
            'upcomingVaccinationsCount'
        ));
    }
}
