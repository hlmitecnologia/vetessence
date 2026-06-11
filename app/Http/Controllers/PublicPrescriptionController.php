<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use Illuminate\Http\Request;

class PublicPrescriptionController extends Controller
{
    public function verify($hash)
    {
        $prescription = Prescription::where('verification_hash', $hash)
            ->with('medicalRecord.pet', 'medicalRecord.vet')
            ->first();

        if (!$prescription) {
            return view('public.verify-prescription', [
                'valid' => false,
                'message' => 'Prescrição não encontrada ou hash inválido.',
            ]);
        }

        if (!$prescription->verified_at) {
            $prescription->update(['verified_at' => now()]);
        }

        $data = [
            'valid' => true,
            'message' => 'Prescrição válida e verificada.',
            'prescription' => $prescription,
            'pet_name' => $prescription->medicalRecord?->pet?->name ?? 'N/A',
            'tutor_name' => $prescription->medicalRecord?->pet?->tutors?->first()?->name ?? 'N/A',
            'medication' => $prescription->medication,
            'dosage' => $prescription->dosage,
            'frequency' => $prescription->frequency,
            'duration' => $prescription->duration,
            'instructions' => $prescription->instructions,
            'vet_name' => $prescription->medicalRecord?->vet?->name ?? 'N/A',
            'crmv' => $prescription->medicalRecord?->vet?->crmv ?? 'N/A',
            'signed_at' => $prescription->signed_at,
            'created_at' => $prescription->created_at,
        ];

        if (auth()->check()) {
            return view('prescriptions.verify', [
                'valid' => true,
                'message' => 'Prescrição válida e verificada.',
                'prescription' => $prescription,
                'pet_name' => $data['pet_name'],
                'medication' => $data['medication'],
                'dosage' => $data['dosage'],
                'signed_at' => $prescription->signed_at,
            ]);
        }

        return view('public.verify-prescription', $data);
    }
}
