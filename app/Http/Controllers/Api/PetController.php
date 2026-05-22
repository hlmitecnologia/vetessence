<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\PetTutor;
use App\Models\MedicalRecord;
use App\Models\Vaccination;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $query = Pet::with('tutors');

        if ($request->tutor_id) {
            $query->whereHas('tutors', function ($q) use ($request) {
                $q->where('tutors.id', $request->tutor_id);
            });
        }

        if ($request->species) {
            $query->where('species', $request->species);
        }

        $pets = $query->orderBy('name')->paginate(15);

        return response()->json($pets);
    }

    public function show($id)
    {
        $pet = Pet::with(['tutors', 'medicalRecords.vet', 'vaccinations'])->findOrFail($id);

        return response()->json([
            'id' => $pet->id,
            'name' => $pet->name,
            'species' => $pet->species,
            'breed' => $pet->breed,
            'gender' => $pet->gender,
            'birth_date' => $pet->birth_date,
            'weight' => $pet->weight,
            'color' => $pet->color,
            'size' => $pet->size,
            'tutors' => $pet->tutors->map(function ($tutor) {
                return [
                    'id' => $tutor->id,
                    'name' => $tutor->name,
                    'phone' => $tutor->phone,
                    'is_primary' => $tutor->pivot->is_primary,
                ];
            }),
            'medical_records' => $pet->medicalRecords->take(10)->map(function ($record) {
                return [
                    'id' => $record->id,
                    'date' => $record->date,
                    'type' => $record->type,
                    'diagnosis' => $record->diagnosis,
                    'vet' => $record->vet->name ?? null,
                ];
            }),
            'vaccinations' => $pet->vaccinations->map(function ($vac) {
                return [
                    'id' => $vac->id,
                    'vaccine' => $vac->vaccine,
                    'date' => $vac->date,
                    'next_date' => $vac->next_date,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'tutor_id' => 'required|exists:tutors,id',
            'species' => 'required|in:' . implode(',', array_keys(config('species'))),
            'breed' => 'nullable|string|max:100',
            'gender' => 'required|in:male,female',
            'birth_date' => 'nullable|date',
            'weight' => 'nullable|numeric|min:0',
        ]);

        $pet = Pet::create($validated);

        PetTutor::create([
            'pet_id' => $pet->id,
            'tutor_id' => $validated['tutor_id'],
            'is_primary' => true,
            'relationship' => 'proprietário',
        ]);

        return response()->json($pet, 201);
    }

    public function update(Request $request, $id)
    {
        $pet = Pet::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'species' => 'sometimes|in:' . implode(',', array_keys(config('species'))),
            'breed' => 'sometimes|nullable|string|max:100',
            'gender' => 'sometimes|in:male,female',
            'birth_date' => 'sometimes|nullable|date',
            'weight' => 'sometimes|nullable|numeric|min:0',
            'color' => 'sometimes|nullable|string|max:50',
            'microchip' => 'sometimes|nullable|string|max:50',
            'size' => 'sometimes|nullable|in:small,medium,large,giant',
        ]);

        $pet->update($validated);

        return response()->json($pet);
    }

    public function myPets(Request $request)
    {
        $user = $request->user();
        
        if (!$user->tutor) {
            return response()->json(['error' => 'Tutor não encontrado'], 404);
        }

        $pets = $user->tutor->pets()->with('tutors')->get();

        return response()->json($pets->map(function ($pet) {
            return [
                'id' => $pet->id,
                'name' => $pet->name,
                'species' => $pet->species,
                'breed' => $pet->breed,
                'gender' => $pet->gender,
                'birth_date' => $pet->birth_date,
                'weight' => $pet->weight,
                'photo_url' => $pet->photo_url,
                'age' => $pet->age,
            ];
        }));
    }
}
