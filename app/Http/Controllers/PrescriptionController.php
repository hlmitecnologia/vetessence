<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicalRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PrescriptionController extends Controller
{
    public function index(Request $request)
    {
        $query = Prescription::with('medicalRecord.pet');

        $prescriptions = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('prescriptions.index', compact('prescriptions'));
    }

    public function create()
    {
        $medicalRecords = MedicalRecord::with('pet', 'user')
            ->orderBy('date', 'desc')
            ->get();
        return view('prescriptions.create', compact('medicalRecords'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'medical_record_id' => 'required|exists:medical_records,id',
            'medication' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        Prescription::create($validated);

        return redirect()->route('prescriptions.index')->with('success', 'Prescrição criada com sucesso!');
    }

    public function show(Prescription $prescription)
    {
        $prescription->load('medicalRecord.pet');
        return view('prescriptions.show', compact('prescription'));
    }

    public function edit(Prescription $prescription)
    {
        $medicalRecords = MedicalRecord::with('pet', 'user')
            ->orderBy('date', 'desc')
            ->get();
        return view('prescriptions.edit', compact('prescription', 'medicalRecords'));
    }

    public function update(Request $request, Prescription $prescription)
    {
        $validated = $request->validate([
            'medical_record_id' => 'required|exists:medical_records,id',
            'medication' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:100',
            'duration' => 'required|string|max:100',
            'instructions' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $prescription->update($validated);

        return redirect()->route('prescriptions.index')->with('success', 'Prescrição atualizada!');
    }

    public function destroy(Prescription $prescription)
    {
        $prescription->delete();
        return redirect()->back()->with('success', 'Prescrição excluída!');
    }

    public function print(Prescription $prescription)
    {
        $prescription->load('medicalRecord.pet', 'medicalRecord.vet');
        return view('prescriptions.print', compact('prescription'));
    }
}
