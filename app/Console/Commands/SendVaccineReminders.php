<?php

namespace App\Console\Commands;

use App\Models\Vaccination;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

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

        $this->info("Found {$vaccinations->count()} vaccinations to remind.");

        foreach ($vaccinations as $vaccination) {
            $this->sendReminder($vaccination);
            $vaccination->update(['reminder_sent' => true]);
            $this->line("Reminder sent for {$vaccination->pet->name} - {$vaccination->vaccine}");
        }

        return Command::SUCCESS;
    }

    protected function sendReminder(Vaccination $vaccination)
    {
        foreach ($vaccination->pet->tutors as $tutor) {
            if ($tutor->email) {
                Mail::raw(
                    "Olá {$tutor->name},\n\n" .
                    "Lembrando que a vacina {$vaccination->vaccine} do pet {$vaccination->pet->name} " .
                    "está agendada para o dia {$vaccination->next_date->format('d/m/Y')}.\n\n" .
                    "Por favor, entre em contato para confirmar o atendimento.\n\n" .
                    "Att,\nVetEssence",
                    function ($message) use ($tutor, $vaccination) {
                        $message->to($tutor->email)
                            ->subject("Lembrete de Vacina - {$vaccination->pet->name}");
                    }
                );
            }
        }
    }
}
