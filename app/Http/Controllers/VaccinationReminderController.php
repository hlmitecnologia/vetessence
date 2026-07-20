<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use App\Models\VaccinationReminder;
use App\Models\Pet;
use App\Services\Notification\NotificationChannel;
use App\Services\Notification\NotificationService;
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

        $reminders = $query->orderBy('scheduled_date', 'desc')->get();

        $pets = Pet::where('is_active', true)->orderBy('name')->get();

        return view('vaccination-reminders.index', compact('reminders', 'pets'));
    }

    public function create()
    {
        return redirect()->route('vaccination-reminders.index');
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

    public function edit($vaccinationReminder)
    {
        return redirect()->route('vaccination-reminders.index');
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

    public function send(VaccinationReminder $vaccinationReminder)
    {
        $vaccinationReminder->load(['vaccination', 'pet.tutors']);

        $tutor = $vaccinationReminder->pet->tutors->first();

        if (!$tutor) {
            return redirect()->route('vaccination-reminders.index')
                ->with('error', 'Pet não possui tutor cadastrado.');
        }

        $channel = $vaccinationReminder->channel ?? 'email';
        $destination = match ($channel) {
            'whatsapp' => $tutor->phone,
            'sms' => $tutor->phone,
            default => $tutor->email,
        };

        if (!$destination) {
            $vaccinationReminder->update([
                'status' => 'failed',
                'error_message' => "Tutor não possui {$channel} cadastrado.",
            ]);

            NotificationLog::create([
                'pet_id' => $vaccinationReminder->pet_id,
                'tutor_id' => $tutor->id,
                'type' => 'vaccine_reminder',
                'channel' => $channel,
                'destination' => $channel === 'email' ? $tutor->email : $tutor->phone,
                'sent_at' => now(),
                'status' => 'failed',
                'message' => "Lembrete de vacina: {$vaccinationReminder->vaccination->vaccine} para {$vaccinationReminder->pet->name}",
                'error_message' => "Tutor não possui {$channel} cadastrado.",
                'branch_id' => $vaccinationReminder->pet->created_at_branch_id,
            ]);

            return redirect()->route('vaccination-reminders.index')
                ->with('error', "Tutor não possui {$channel} cadastrado.");
        }

        $message = "Olá {$tutor->name},\n\n" .
            "Lembrando que a vacina {$vaccinationReminder->vaccination->vaccine} do pet {$vaccinationReminder->pet->name} " .
            "está agendada para o dia {$vaccinationReminder->scheduled_date->format('d/m/Y')}.\n\n" .
            "Por favor, entre em contato para confirmar o atendimento.\n\n" .
            "Att,\nVetEssence";

        $service = app(NotificationService::class);
        $result = $service->send(
            NotificationChannel::from($channel),
            $destination,
            $message,
            "Lembrete de Vacina - {$vaccinationReminder->pet->name}"
        );

        if ($result->success) {
            $vaccinationReminder->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            NotificationLog::create([
                'pet_id' => $vaccinationReminder->pet_id,
                'tutor_id' => $tutor->id,
                'type' => 'vaccine_reminder',
                'channel' => $channel,
                'destination' => $destination,
                'sent_at' => now(),
                'status' => 'sent',
                'message' => "Lembrete de vacina enviado: {$vaccinationReminder->vaccination->vaccine} para {$vaccinationReminder->pet->name}",
                'error_message' => null,
                'branch_id' => $vaccinationReminder->pet->created_at_branch_id,
            ]);

            return redirect()->route('vaccination-reminders.index')
                ->with('success', 'Lembrete enviado com sucesso!');
        }

        $vaccinationReminder->update([
            'status' => 'failed',
            'error_message' => $result->error ?? 'Erro desconhecido ao enviar.',
        ]);

        NotificationLog::create([
            'pet_id' => $vaccinationReminder->pet_id,
            'tutor_id' => $tutor->id,
            'type' => 'vaccine_reminder',
            'channel' => $channel,
            'destination' => $destination,
            'sent_at' => now(),
            'status' => 'failed',
            'message' => "Lembrete de vacina: {$vaccinationReminder->vaccination->vaccine} para {$vaccinationReminder->pet->name}",
            'error_message' => $result->error ?? 'Erro desconhecido ao enviar.',
            'branch_id' => $vaccinationReminder->pet->created_at_branch_id,
        ]);

        return redirect()->route('vaccination-reminders.index')
            ->with('error', 'Erro ao enviar lembrete: ' . ($result->error ?? 'Erro desconhecido.'));
    }

    public function destroy(VaccinationReminder $vaccinationReminder)
    {
        $vaccinationReminder->delete();

        return redirect()->route('vaccination-reminders.index')
            ->with('success', 'Lembrete de vacina excluído!');
    }
}
