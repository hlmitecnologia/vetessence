<?php

namespace App\Console\Commands;

use App\Models\CommunicationTemplate;
use App\Models\NotificationLog;
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
            ->where('channel', 'email')
            ->where('is_active', true)
            ->first();

        $pets = Pet::whereMonth('birth_date', now()->month)
            ->whereDay('birth_date', now()->day)
            ->with('tutors')
            ->get();

        $sent = 0;

        foreach ($pets as $pet) {
            $alreadySent = NotificationLog::where('pet_id', $pet->id)
                ->where('type', 'birthday')
                ->whereDate('created_at', today())
                ->exists();

            if ($alreadySent) continue;

            foreach ($pet->tutors as $tutor) {
                if (!$tutor->email) continue;

                $message = $template
                    ? str_replace(['{pet_name}', '{age}'], [$pet->name, $pet->age ?? 'N/A'], $template->content)
                    : "Feliz aniversário, {$pet->name}! 🎉";

                NotificationLog::create([
                    'pet_id' => $pet->id,
                    'tutor_id' => $tutor->id,
                    'type' => 'birthday',
                    'channel' => 'email',
                    'destination' => $tutor->email,
                    'message' => $message,
                    'status' => 'pending',
                ]);
                $sent++;
            }
        }

        $this->info("Sent {$sent} birthday greetings.");
    }
}
