<?php

namespace App\Http\Controllers;

use App\Models\Teleconsultation;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class TeleconsultationController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:teleconsulta');
    }
    public function index(Request $request)
    {
        $query = Teleconsultation::with(['pet', 'vet', 'tutor', 'appointment']);

        if ($request->status) $query->where('status', $request->status);
        if ($request->search) {
            $query->whereHas('pet', fn($q) => $q->where('name', 'like', "%{$request->search}%"));
        }

        $teleconsultations = $query->orderBy('scheduled_at', 'desc')->get();

        return view('teleconsultations.index', compact('teleconsultations'));
    }

    public function create()
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $vets = User::where('is_active', true)->where(fn($q) => $q->role('veterinario')->orWhere('is_veterinarian', true))->orderBy('name')->get();
        return view('teleconsultations.create', compact('pets', 'vets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'nullable|exists:users,id',
            'vet_id' => 'required|exists:users,id',
            'provider' => 'required|string|max:50',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $pet = Pet::find($validated['pet_id']);
        $validated['room_token'] = Teleconsultation::generateRoomToken();
        $validated['status'] = 'scheduled';

        if ($validated['provider'] === 'jitsi') {
            $validated['provider_url'] = 'https://meet.jit.si/' . config('app.name') . '-' . $validated['room_token'];
        }

        Teleconsultation::create($validated);

        return redirect()->route('teleconsultations.index')
            ->with('success', 'Teleconsulta agendada com sucesso!');
    }

    public function show(Teleconsultation $teleconsultation)
    {
        $teleconsultation->load(['pet', 'vet', 'tutor', 'appointment']);
        return view('teleconsultations.show', compact('teleconsultation'));
    }

    public function edit(Teleconsultation $teleconsultation)
    {
        $pets = Pet::where('is_active', true)->orderBy('name')->get();
        $vets = User::where('is_active', true)->where(fn($q) => $q->role('veterinario')->orWhere('is_veterinarian', true))->orderBy('name')->get();
        return view('teleconsultations.edit', compact('teleconsultation', 'pets', 'vets'));
    }

    public function update(Request $request, Teleconsultation $teleconsultation)
    {
        $validated = $request->validate([
            'room_name' => 'required|string|max:255',
            'pet_id' => 'required|exists:pets,id',
            'tutor_id' => 'nullable|exists:users,id',
            'vet_id' => 'required|exists:users,id',
            'provider' => 'required|string|max:50',
            'scheduled_at' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $teleconsultation->update($validated);

        return redirect()->route('teleconsultations.show', $teleconsultation)
            ->with('success', 'Teleconsulta atualizada!');
    }

    public function destroy(Teleconsultation $teleconsultation)
    {
        $teleconsultation->delete();
        return redirect()->route('teleconsultations.index')->with('success', 'Teleconsulta excluída.');
    }

    public function start(Teleconsultation $teleconsultation)
    {
        $teleconsultation->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        return redirect()->away($teleconsultation->room_url);
    }

    public function end(Request $request, Teleconsultation $teleconsultation)
    {
        $ended = now();
        $duration = $teleconsultation->started_at ? $teleconsultation->started_at->diffInMinutes($ended) : null;

        $teleconsultation->update([
            'status' => 'completed',
            'ended_at' => $ended,
            'duration_minutes' => $duration,
            'notes' => $request->notes ?? $teleconsultation->notes,
        ]);

        return redirect()->route('teleconsultations.show', $teleconsultation)
            ->with('success', 'Teleconsulta encerrada.');
    }

    public function room($token)
    {
        $teleconsultation = Teleconsultation::where('room_token', $token)->firstOrFail();

        if ($teleconsultation->status === 'scheduled') {
            $teleconsultation->update(['status' => 'active', 'started_at' => now()]);
        }

        return view('teleconsultations.room', compact('teleconsultation'));
    }
}
