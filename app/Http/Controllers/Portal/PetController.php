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
        $upcomingAppointments = $pet->appointments()->where('date', '>=', today())->orderBy('date')->orderBy('time')->get();
        $pastAppointments = $pet->appointments()->where('date', '<', today())->orderBy('date', 'desc')->orderBy('time', 'desc')->take(10)->get();
        $vaccinations = $pet->vaccinations()->orderBy('date', 'desc')->take(10)->get();
        return view('portal.pets.show', compact('pet', 'upcomingAppointments', 'pastAppointments', 'vaccinations'));
    }
}
