<?php

namespace App\Console\Commands;

use App\Models\CommunicationQueue;
use App\Models\Vaccination;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendVaccineReminders extends Command
{
    protected $signature = 'vaccines:remind {--days=7 : Number of days to look ahead}';
    protected $description = 'Send reminders for upcoming vaccinations';

    public function handle()
    {
        $days = (int) $this->option('days');
        $targetDate = Carbon::today()->addDays($days);

        $vaccinations = Vaccination::with(['pet.tutors'])
            ->whereNotNull('next_date')
            ->where('next_date', '<=', $targetDate)
            ->where('next_date', '>=', Carbon::today())
            ->where('reminder_sent', false)
            ->get();

        $this->info("Encontrada(s) {$vaccinations->count()} vacinação(ões) para lembrete.");
        $sent = 0;

        foreach ($vaccinations as $vaccination) {
            foreach ($vaccination->pet->tutors as $tutor) {
                if (!$tutor->email && !$tutor->phone) continue;

                $channel = 'email';
                $destination = $tutor->email;

                if ($tutor->notify_whatsapp && $tutor->phone) {
                    $channel = 'whatsapp';
                    $destination = $tutor->phone;
                }

                $message = "Olá {$tutor->name},\n\n" .
                    "Lembrando que a vacina {$vaccination->vaccine} do pet {$vaccination->pet->name} " .
                    "está agendada para o dia {$vaccination->next_date->format('d/m/Y')}.\n\n" .
                    "Por favor, entre em contato para confirmar o atendimento.\n\n" .
                    "Att,\nVetEssence";

                CommunicationQueue::create([
                    'pet_id' => $vaccination->pet_id,
                    'tutor_id' => $tutor->id,
                    'channel' => $channel,
                    'destination' => $destination,
                    'message_content' => $message,
                    'scheduled_at' => now(),
                    'status' => 'pending',
                ]);
                $sent++;
            }

            $vaccination->update(['reminder_sent' => true]);
            $this->line("Lembrete enfileirado para {$vaccination->pet->name} - {$vaccination->vaccine}");
        }

        $this->info("Enfileirados {$sent} lembretes de vacina.");
        return Command::SUCCESS;
    }
}
