<?php

namespace App\Http\Controllers;

use App\Models\Prescription;
use App\Models\MedicalRecord;

class SignatureVerifyController extends Controller
{
    public function verify($model, $id)
    {
        $mapping = [
            'prescription' => Prescription::class,
            'medical-record' => MedicalRecord::class,
        ];

        $class = $mapping[$model] ?? null;
        if (!$class) {
            abort(404);
        }

        $record = $class::findOrFail($id);

        if (!$record->isSigned()) {
            return view('signature-verify', [
                'valid' => false,
                'message' => 'Documento não assinado digitalmente.',
                'record' => $record,
                'model' => $model,
            ]);
        }

        $valid = $record->verifyIntegrity();
        $signedAt = $record->signed_at;

        return view('signature-verify', compact('valid', 'signedAt', 'record', 'model'));
    }
}
