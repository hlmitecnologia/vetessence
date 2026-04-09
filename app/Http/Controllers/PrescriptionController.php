<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with('medicalRecord.pet');

        $prescriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function show(Prescription $prescription)
    {
        $prescription->load('medicalRecord.pet');
        return view('prescriptions.show', compact('prescription'));
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->back()->with('success', 'Prescrição excluída!');
    }
}
