<?php

namespace App\Console\Commands;

use App\Models\CommunicationQueue;
use App\Models\CommunicationTemplate;
use App\Models\Pet;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessBirthdayCampaigns extends Command
{
    protected $signature = 'birthday:process';
    protected $description = 'Send birthday greetings to pets';

    public function handle()
    {
        $template = CommunicationTemplate::where('type', 'birthday')
            ->where('is_active', true)
            ->first();

        $pets = Pet::whereMonth('birth_date', now()->month)
            ->whereDay('birth_date', now()->day)
            ->with('tutors')
            ->get();

        $sent = 0;

        foreach ($pets as $pet) {
            $alreadySent = CommunicationQueue::where('pet_id', $pet->id)
                ->where('channel', 'email')
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadySent) continue;

            foreach ($pet->tutors as $tutor) {
                if (!$tutor->email && !$tutor->phone) continue;

                $channel = 'email';
                $destination = $tutor->email;

                if ($tutor->notify_whatsapp && $tutor->phone) {
                    $channel = 'whatsapp';
                    $destination = $tutor->phone;
                }

                $message = $template
                    ? str_replace(['{pet_name}', '{age}'], [$pet->name, $pet->age ?? 'N/A'], $template->content)
                    : "Feliz aniversário, {$pet->name}! 🎉";

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
                $sent++;
            }
        }

        $this->info("Enviados {$sent} cumprimentos de aniversário.");
    }
}
