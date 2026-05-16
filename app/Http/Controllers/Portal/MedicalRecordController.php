<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $pets = auth()->user()->pets()->with('medicalRecords.vet')->get();
        $records = $pets->flatMap->medicalRecords->sortByDesc('created_at');
        return view('portal.medical-records.index', compact('records'));
    }

    public function show($id)
    {
        $pet = auth()->user()->pets()->with('medicalRecords.vet')->findOrFail($id);
        return view('portal.medical-records.show', compact('pet'));
    }
}
