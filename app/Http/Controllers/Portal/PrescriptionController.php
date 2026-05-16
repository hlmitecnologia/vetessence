<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index()
    {
        $pets = auth()->user()->pets()->with('medicalRecords.prescriptions')->get();
        $prescriptions = $pets->flatMap->medicalRecords->flatMap->prescriptions->sortByDesc('created_at');
        return view('portal.prescriptions.index', compact('prescriptions'));
    }
}
