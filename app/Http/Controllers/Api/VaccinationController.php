<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vaccination;
use Illuminate\Http\Request;

class VaccinationController extends Controller
{
    public function index(Request $request)
    {
        $query = Vaccination::with('pet');

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $vaccinations = $query->orderBy('date', 'desc')->paginate(20);

        return response()->json($vaccinations);
    }

    public function show($id)
    {
        $vaccination = Vaccination::with('pet')->find($id);

        if (!$vaccination) {
            return response()->json(['error' => 'Vacinação não encontrada'], 404);
        }

        return response()->json($vaccination);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'vaccine' => 'required|string|max:255',
            'date' => 'required|date',
            'batch' => 'nullable|string|max:50',
            'veterinarian' => 'nullable|string|max:255',
            'next_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $vaccination = Vaccination::create($validated);

        return response()->json($vaccination, 201);
    }

    public function byPet($petId)
    {
        $vaccinations = Vaccination::where('pet_id', $petId)
            ->orderBy('date', 'desc')
            ->get();

        return response()->json($vaccinations);
    }
}
