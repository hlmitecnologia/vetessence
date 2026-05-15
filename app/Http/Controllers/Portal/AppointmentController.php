<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppointmentController extends Controller
{
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        $petIds = $tutor->pets()->pluck('pets.id');

        $upcoming = Appointment::whereIn('pet_id', $petIds)
            ->where('date', '>=', today())
            ->orderBy('date')
            ->orderBy('time')
            ->get();

        $past = Appointment::whereIn('pet_id', $petIds)
            ->where('date', '<', today())
            ->orderBy('date', 'desc')
            ->orderBy('time', 'desc')
            ->take(20)
            ->get();

        return view('portal.appointments.index', compact('upcoming', 'past'));
    }

    public function create()
    {
        $pets = Auth::guard('tutor')->user()->pets;
        return view('portal.appointments.create', compact('pets'));
    }
}
