<?php

namespace App\Http\Controllers;

use App\Models\VaccinationReminder;
use App\Models\Pet;
use Illuminate\Http\Request;

class VaccinationReminderController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:lembrete-vacinas');
    }

    public function index(Request $request)
    {
        $query = VaccinationReminder::with(['vaccination', 'pet']);

        if ($request->search) {
            $query->whereHas('pet', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%");
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->pet_id) {
            $query->where('pet_id', $request->pet_id);
        }

        $reminders = $query->orderBy('scheduled_date', 'desc')->paginate(20);

        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('vaccination-reminders.index', compact('reminders', 'pets'));
    }

    public function create()
    {
        $pets = Pet::with('vaccinations')->where('is_active', true)->orderBy('name')->get();
        return view('vaccination-reminders.create', compact('pets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'vaccination_id' => 'required|exists:vaccinations,id',
            'pet_id' => 'required|exists:pets,id',
            'scheduled_date' => 'required|date',
            'channel' => 'nullable|string|max:20',
            'status' => 'required|string|max:20',
            'notes' => 'nullable|string|max:500',
        ]);

        VaccinationReminder::create($validated);

        return redirect()->route('vaccination-reminders.index')
            ->with('success', 'Lembrete de vacina cadastrado!');
    }

    public function show(VaccinationReminder $vaccinationReminder)
    {
        $vaccinationReminder->load(['vaccination', 'pet']);
        return view('vaccination-reminders.show', compact('vaccinationReminder'));
    }

    public function edit(VaccinationReminder $vaccinationReminder)
    {
        $vaccinationReminder->load(['vaccination', 'pet']);
        $pets = Pet::with('vaccinations')->where('is_active', true)->orderBy('name')->get();
        return view('vaccination-reminders.edit', compact('vaccinationReminder', 'pets'));
    }

    public function update(Request $request, VaccinationReminder $vaccinationReminder)
    {
        $validated = $request->validate([
            'vaccination_id' => 'required|exists:vaccinations,id',
            'pet_id' => 'required|exists:pets,id',
            'scheduled_date' => 'required|date',
            'sent_at' => 'nullable|date',
            'channel' => 'nullable|string|max:20',
            'status' => 'required|string|max:20',
            'error_message' => 'nullable|string|max:500',
        ]);

        $vaccinationReminder->update($validated);

        return redirect()->route('vaccination-reminders.index')
            ->with('success', 'Lembrete de vacina atualizado!');
    }

    public function destroy(VaccinationReminder $vaccinationReminder)
    {
        $vaccinationReminder->delete();

        return redirect()->route('vaccination-reminders.index')
            ->with('success', 'Lembrete de vacina excluído!');
    }
}
