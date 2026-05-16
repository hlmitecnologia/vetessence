<?php

namespace App\Console\Commands;

use App\Models\CommunicationQueue;
use App\Services\EmailApiService;
use App\Services\Communication\WhatsAppProvider;
use App\Services\Communication\SmsProvider;
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

        $whatsapp = new WhatsAppProvider();
        $sms = new SmsProvider();

        foreach ($items as $item) {
            $success = false;

            if ($item->channel === 'email') {
                $tutorName = $item->tutor ? $item->tutor->name : 'Tutor';
                $success = $email->send(
                    $tutorName,
                    $item->destination,
                    $item->message_content
                );
            } elseif ($item->channel === 'whatsapp') {
                $success = $whatsapp->send($item->destination, $item->message_content);
            } elseif ($item->channel === 'sms') {
                $success = $sms->send($item->destination, $item->message_content);
            } else {
                $item->update(['status' => 'failed', 'error_message' => 'Unsupported channel']);
                $failed++;
                continue;
            }

            if ($success) {
                $item->update(['sent_at' => now(), 'status' => 'sent']);
                $sent++;
            } else {
                $item->update(['status' => 'failed', 'error_message' => 'Provider rejected']);
                $failed++;
            }
        }

        $this->info("Queue processed: {$sent} sent, {$failed} failed.");
        return Command::SUCCESS;
    }
}
