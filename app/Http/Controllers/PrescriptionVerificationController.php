<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PrescriptionVerificationController extends Controller
{
    public function verify($hash)
    {
        $prescription = Prescription::where('verification_hash', $hash)->first();

        if (!$prescription) {
            return view('prescriptions.verify', ['valid' => false, 'message' => 'Prescrição não encontrada ou hash inválido.']);
        }

        if (!$prescription->verified_at) {
            $prescription->update(['verified_at' => now()]);
        }

        return view('prescriptions.verify', [
            'valid' => true,
            'message' => 'Prescrição válida e verificada.',
            'prescription' => $prescription,
            'pet_name' => $prescription->medicalRecord->pet->name ?? 'N/A',
            'medication' => $prescription->medication,
            'dosage' => $prescription->dosage,
            'signed_at' => $prescription->signed_at,
        ]);
    }
}
