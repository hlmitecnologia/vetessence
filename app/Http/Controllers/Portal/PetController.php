<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PetController extends Controller
{
    public function index()
    {
        $pets = Auth::guard('tutor')->user()->pets;
        return view('portal.pets.index', compact('pets'));
    }

    public function show($id)
    {
        $pet = Auth::guard('tutor')->user()->pets()->findOrFail($id);
        $upcomingAppointments = $pet->appointments()->where('start_time', '>=', now())->orderBy('start_time')->get();
        $pastAppointments = $pet->appointments()->where('start_time', '<', now())->orderBy('start_time', 'desc')->take(10)->get();
        $vaccinations = $pet->vaccinations()->orderBy('applied_date', 'desc')->take(10)->get();
        return view('portal.pets.show', compact('pet', 'upcomingAppointments', 'pastAppointments', 'vaccinations'));
    }
}
