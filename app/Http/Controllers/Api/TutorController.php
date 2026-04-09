<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use Illuminate\Http\Request;

class TutorController extends Controller
{
    public function index(Request $request)
    {
        $query = Tutor::query();

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $tutors = $query->with('user')->orderBy('name')->paginate(15);

        return response()->json($tutors);
    }

    public function show($id)
    {
        $tutor = Tutor::with(['pets', 'invoices'])->findOrFail($id);

        return response()->json([
            'id' => $tutor->id,
            'name' => $tutor->name,
            'cpf' => $tutor->cpf,
            'phone' => $tutor->phone,
            'email' => $tutor->email,
            'address' => $tutor->address,
            'city' => $tutor->city,
            'state' => $tutor->state,
            'pets' => $tutor->pets->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species,
                    'breed' => $pet->breed,
                ];
            }),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'cpf' => 'required|cpf|unique:tutors',
            'email' => 'required|email|unique:tutors',
            'phone' => 'required',
        ]);

        $tutor = Tutor::create($validated);

        return response()->json($tutor, 201);
    }

    public function update(Request $request, $id)
    {
        $tutor = Tutor::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string',
            'phone_secondary' => 'sometimes|nullable|string',
            'email' => 'sometimes|email|unique:tutors,email,' . $id,
            'address' => 'sometimes|nullable|string',
            'city' => 'sometimes|nullable|string',
            'state' => 'sometimes|nullable|string|max:2',
        ]);

        $tutor->update($validated);

        return response()->json($tutor);
    }

    public function myTutor(Request $request)
    {
        $user = $request->user();
        
        if (!$user->tutor) {
            return response()->json(['error' => 'Tutor não encontrado'], 404);
        }

        $tutor = $user->tutor->load('pets');

        return response()->json([
            'id' => $tutor->id,
            'name' => $tutor->name,
            'cpf' => $tutor->cpf,
            'phone' => $tutor->phone,
            'email' => $tutor->email,
            'address' => $tutor->address,
            'city' => $tutor->city,
            'state' => $tutor->state,
            'pets' => $tutor->pets->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'species' => $pet->species,
                    'breed' => $pet->breed,
                    'photo_url' => $pet->photo_url,
                ];
            }),
        ]);
    }
}
