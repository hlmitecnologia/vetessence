<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\TherapySession;
use App\Models\User;
use Illuminate\Http\Request;

class TherapySessionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:terapias');
    }

    public function index()
    {
        $sessions = TherapySession::with(['pet', 'therapist'])->latest('session_date')->paginate(20);
        return view('therapy-sessions.index', compact('sessions'));
    }

    public function create()
    {
        $pets = Pet::orderBy('name')->get();
        $therapists = User::where(fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', 'veterinario'))->orWhere('is_veterinarian', true))->orWhereHas('roles', fn($q) => $q->where('name', 'fisioterapeuta'))->orderBy('name')->get();
        return view('therapy-sessions.create', compact('pets', 'therapists'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|string|max:50',
            'session_date' => 'required|date',
            'therapist_id' => 'nullable|exists:users,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'observations' => 'nullable|string',
            'status' => 'required|string|max:20',
        ]);

        TherapySession::create($data);

        return redirect()->route('therapy-sessions.index')
            ->with('success', 'Sessão de terapia criada.');
    }

    public function show(TherapySession $therapySession)
    {
        $therapySession->load(['pet', 'therapist']);
        return view('therapy-sessions.show', compact('therapySession'));
    }

    public function edit(TherapySession $therapySession)
    {
        $pets = Pet::orderBy('name')->get();
        $therapists = User::where(fn($q) => $q->whereHas('roles', fn($q) => $q->where('name', 'veterinario'))->orWhere('is_veterinarian', true))->orWhereHas('roles', fn($q) => $q->where('name', 'fisioterapeuta'))->orderBy('name')->get();
        return view('therapy-sessions.edit', compact('therapySession', 'pets', 'therapists'));
    }

    public function update(Request $request, TherapySession $therapySession)
    {
        $data = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'type' => 'required|string|max:50',
            'session_date' => 'required|date',
            'therapist_id' => 'nullable|exists:users,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'observations' => 'nullable|string',
            'status' => 'required|string|max:20',
        ]);

        $therapySession->update($data);

        return redirect()->route('therapy-sessions.index')
            ->with('success', 'Sessão de terapia atualizada.');
    }

    public function destroy(TherapySession $therapySession)
    {
        $therapySession->delete();
        return redirect()->route('therapy-sessions.index')
            ->with('success', 'Sessão de terapia removida.');
    }
}
