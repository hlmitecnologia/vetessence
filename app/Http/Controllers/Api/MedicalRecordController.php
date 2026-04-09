<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalRecord;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = MedicalRecord::with(['pet', 'vet']);

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $records = $query->orderBy('date', 'desc')->paginate(20);

        return response()->json($records);
    }

    public function show($id)
    {
        $record = MedicalRecord::with(['pet', 'vet', 'prescriptions'])->find($id);

        if (!$record) {
            return response()->json(['error' => 'Prontuário não encontrado'], 404);
        }

        return response()->json($record);
    }

    public function byPet($petId)
    {
        $records = MedicalRecord::with('vet')
            ->where('pet_id', $petId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($records);
    }
}
