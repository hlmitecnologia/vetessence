<?php

namespace App\Console\Commands;

use App\Models\CommunicationQueue;
use App\Services\EmailApiService;
use Illuminate\Console\Command;

class ProcessCommunicationQueue extends Command
{
    protected $signature = 'queue:process {--limit=50 : Max items to process per run}';
    protected $description = 'Process pending communication queue items';

    public function handle(EmailApiService $email)
    {
        $limit = (int) $this->option('limit');
        $sent = 0;
        $failed = 0;

        $items = CommunicationQueue::whereNull('sent_at')
            ->where('scheduled_at', '<=', now())
            ->where('status', 'pending')
            ->limit($limit)
            ->get();

        if ($items->isEmpty()) {
            $this->info('No pending queue items.');
            return Command::SUCCESS;
        }

        foreach ($items as $item) {
            if ($item->channel === 'email') {
                $tutorName = $item->tutor ? $item->tutor->name : 'Tutor';
                $success = $email->send(
                    $tutorName,
                    $item->destination,
                    $item->message_content
                );

                if ($success) {
                    $item->update(['sent_at' => now(), 'status' => 'sent']);
                    $sent++;
                } else {
                    $item->update(['status' => 'failed', 'error_message' => 'Email API rejected']);
                    $failed++;
                }
            } elseif ($item->channel === 'whatsapp') {
                // WhatsApp is handled externally by the WAHA reminder service
                $item->update(['status' => 'sent']);
                $sent++;
            } else {
                $item->update(['status' => 'failed', 'error_message' => 'Unsupported channel']);
                $failed++;
            }
        }

        $this->info("Queue processed: {$sent} sent, {$failed} failed.");
        return Command::SUCCESS;
    }
}
