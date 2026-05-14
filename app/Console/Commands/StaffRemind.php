<?php

namespace App\Console\Commands;

use App\Models\StaffSchedule;
use App\Models\User;
use App\Services\EmailApiService;
use Illuminate\Console\Command;

class StaffRemind extends Command
{
    protected $signature = 'staff:remind {--days=1 : Number of days ahead to check}';
    protected $description = 'Send reminders to staff about upcoming shifts and on-call duty';

    public function handle(EmailApiService $email)
    {
        $date = now()->addDays((int) $this->option('days'))->format('Y-m-d');

        $schedules = StaffSchedule::with('user')
            ->where('work_date', $date)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info('Nenhum agendamento encontrado para ' . $date);
            return 0;
        }

        $sent = 0;
        foreach ($schedules as $schedule) {
            if (!$schedule->user || !$schedule->user->email) {
                continue;
            }

            $type = $schedule->is_on_call
                ? 'Plantão (' . ($schedule->on_call_type ?? 'geral') . ')'
                : 'Escala regular (' . $schedule->shift_type . ')';

            $message = "Olá {$schedule->user->name}!\n\n"
                . "Você tem {$type} agendada para amanhã ({$schedule->work_date->format('d/m/Y')}).\n"
                . "Horário: {$schedule->start_time} às {$schedule->end_time}\n\n"
                . "Atenciosamente,\nEquipe VetEssence";

            $success = $email->send($schedule->user->name, $schedule->user->email, $message);

            if ($success) {
                $sent++;
                $this->info("Lembrete enviado para {$schedule->user->email}");
            } else {
                $this->error("Falha ao enviar para {$schedule->user->email}");
            }
        }

        $this->info("{$sent} lembretes enviados.");
        return 0;
    }
}
