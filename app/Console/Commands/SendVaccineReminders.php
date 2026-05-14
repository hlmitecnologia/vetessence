<?php

namespace App\Console\Commands;

use App\Models\Vaccination;
use App\Services\EmailApiService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendVaccineReminders extends Command
{
    protected $signature = 'vaccines:remind {--days=7 : Number of days to look ahead}';
    protected $description = 'Send reminders for upcoming vaccinations';

    public function handle(EmailApiService $email)
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
            $this->sendReminder($vaccination, $email);
            $vaccination->update(['reminder_sent' => true]);
            $this->line("Reminder sent for {$vaccination->pet->name} - {$vaccination->vaccine}");
        }

        return Command::SUCCESS;
    }

    protected function sendReminder(Vaccination $vaccination, EmailApiService $email): void
    {
        foreach ($vaccination->pet->tutors as $tutor) {
            if ($tutor->email) {
                $message = "Olá {$tutor->name},\n\n" .
                    "Lembrando que a vacina {$vaccination->vaccine} do pet {$vaccination->pet->name} " .
                    "está agendada para o dia {$vaccination->next_date->format('d/m/Y')}.\n\n" .
                    "Por favor, entre em contato para confirmar o atendimento.\n\n" .
                    "Att,\nVetEssence";

                $email->send($tutor->name, $tutor->email, $message);
            }
        }
    }
}
