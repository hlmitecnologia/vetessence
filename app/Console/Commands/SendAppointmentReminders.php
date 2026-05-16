<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\CommunicationQueue;
use App\Models\CommunicationTemplate;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendAppointmentReminders extends Command
{
    protected $signature = 'appointments:remind {--days=1 : Days ahead to remind}';
    protected $description = 'Queue appointment reminders via CommunicationQueue';

    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = Carbon::today()->addDays($days);

        $appointments = Appointment::with(['pet.tutors', 'branch'])
            ->whereDate('date', $targetDate)
            ->where('status', 'scheduled')
            ->get();

        $this->info("Found {$appointments->count()} appointments to remind.");

        $template = CommunicationTemplate::where('type', 'appointment_reminder')->first();

        foreach ($appointments as $appointment) {
            foreach ($appointment->pet->tutors as $tutor) {
                $channel = ($tutor->phone && $tutor->notify_whatsapp) ? 'whatsapp' : 'email';
                $destination = $tutor->phone ?? $tutor->email;

                if ($channel === 'whatsapp' && !$tutor->notify_whatsapp) continue;
                if ($channel === 'email' && !$tutor->notify_email) continue;
                if (!$destination) continue;

                $time = $appointment->time instanceof Carbon ? $appointment->time->format('H:i') : $appointment->time;
                $message = "Olá {$tutor->name}! Lembramos que {$appointment->pet->name} tem consulta agendada para {$appointment->date->format('d/m/Y')} às {$time}. Confirme sua presença!";

                CommunicationQueue::create([
                    'tutor_id' => $tutor->id,
                    'pet_id' => $appointment->pet_id,
                    'template_id' => $template ? $template->id : null,
                    'channel' => $channel,
                    'destination' => $destination,
                    'message_content' => $message,
                    'status' => 'pending',
                    'branch_id' => $appointment->branch_id,
                ]);

                $this->line("Queued reminder for {$tutor->name} / {$appointment->pet->name}");
            }
        }

        return Command::SUCCESS;
    }
}
