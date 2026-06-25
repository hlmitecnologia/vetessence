<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Vaccination;
use Illuminate\Support\Facades\Auth;

class VaccinationController extends Controller
{
    public function index()
    {
        $tutor = Auth::guard('tutor')->user();
        $petIds = $tutor->pets()->pluck('pets.id');

        $vaccinations = Vaccination::with('pet')
            ->whereIn('pet_id', $petIds)
            ->whereNotNull('next_date')
            ->where('next_date', '>=', today())
            ->orderBy('next_date')
            ->get();

        return view('portal.vaccinations.index', compact('vaccinations'));
    }
}
