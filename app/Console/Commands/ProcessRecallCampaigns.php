<?php

namespace App\Console\Commands;

use App\Models\CommunicationTemplate;
use App\Models\NotificationLog;
use App\Models\Pet;
use App\Models\Tutor;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessRecallCampaigns extends Command
{
    protected $signature = 'recall:process';
    protected $description = 'Process recall campaigns for overdue vaccinations';

    public function handle()
    {
        $templates = CommunicationTemplate::where('type', 'recall')->where('is_active', true)->get();

        $processed = 0;

        $pets = Pet::whereHas('vaccinations', function ($q) {
            $q->whereNotNull('next_date')
              ->where('next_date', '<', now()->subDays(7));
        })->with('tutors', 'vaccinations')->get();

        foreach ($pets as $pet) {
            $lastVaccine = $pet->vaccinations()
                ->whereNotNull('next_date')
                ->latest('next_date')
                ->first();

            if (!$lastVaccine) continue;

            $alreadyNotified = NotificationLog::where('pet_id', $pet->id)
                ->where('type', 'recall')
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();

            if ($alreadyNotified) continue;

            foreach ($pet->tutors as $tutor) {
                if (!$tutor->email && !$tutor->phone) continue;

                $template = $templates->firstWhere('channel', 'email');
                $message = $template
                    ? str_replace(['{pet_name}', '{vaccine_name}'], [$pet->name, $lastVaccine->vaccine], $template->content)
                    : "Olá, lembrete: seu pet {$pet->name} está com a vacina {$lastVaccine->vaccine} atrasada. Procure-nos para atualização.";

                if ($tutor->email) {
                    NotificationLog::create([
                        'pet_id' => $pet->id,
                        'tutor_id' => $tutor->id,
                        'type' => 'recall',
                        'channel' => 'email',
                        'destination' => $tutor->email,
                        'message' => $message,
                        'status' => 'pending',
                    ]);
                    $processed++;
                }
            }
        }

        $this->info("Processed {$processed} recall notifications.");
    }
}
