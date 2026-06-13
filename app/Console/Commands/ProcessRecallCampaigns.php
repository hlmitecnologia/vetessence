<?php

namespace App\Console\Commands;

use App\Models\CommunicationQueue;
use App\Models\CommunicationTemplate;
use App\Models\Pet;
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

            $alreadyNotified = CommunicationQueue::where('pet_id', $pet->id)
                ->where('channel', 'email')
                ->where('created_at', '>=', now()->subDays(30))
                ->exists();

            if ($alreadyNotified) continue;

            foreach ($pet->tutors as $tutor) {
                if (!$tutor->email && !$tutor->phone) continue;

                $channel = 'email';
                $destination = $tutor->email;

                if ($tutor->notify_whatsapp && $tutor->phone) {
                    $channel = 'whatsapp';
                    $destination = $tutor->phone;
                }

                $template = $templates->firstWhere('channel', $channel);
                $message = $template
                    ? str_replace(['{pet_name}', '{vaccine_name}'], [$pet->name, $lastVaccine->vaccine], $template->content)
                    : "Olá, lembrete: seu pet {$pet->name} está com a vacina {$lastVaccine->vaccine} atrasada. Procure-nos para atualização.";

                CommunicationQueue::create([
                    'pet_id' => $pet->id,
                    'tutor_id' => $tutor->id,
                    'template_id' => $template?->id,
                    'channel' => $channel,
                    'destination' => $destination,
                    'message_content' => $message,
                    'scheduled_at' => now(),
                    'status' => 'pending',
                ]);
                $processed++;
            }
        }

        $this->info("Processadas {$processed} notificações de recall.");
    }
}
