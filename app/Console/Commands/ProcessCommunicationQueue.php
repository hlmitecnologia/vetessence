<?php

namespace App\Console\Commands;

use App\Models\CommunicationQueue;
use App\Services\Notification\NotificationChannel;
use App\Services\Notification\NotificationService;
use Illuminate\Console\Command;

class ProcessCommunicationQueue extends Command
{
    protected $signature = 'queue:process {--limit=50 : Max items to process per run}';
    protected $description = 'Process pending communication queue items';

    public function handle(NotificationService $notificationService)
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
            $channel = NotificationChannel::tryFrom($item->channel);

            if (!$channel) {
                $item->update(['status' => 'failed', 'error_message' => 'Unsupported channel']);
                $failed++;
                continue;
            }

            $subject = null;
            if ($channel === NotificationChannel::Email) {
                $template = $item->template;
                $subject = $template?->subject ?? 'Comunicação';
            }

            $result = $notificationService->send(
                $channel,
                $item->destination,
                $item->message_content,
                $subject
            );

            if ($result->success) {
                $item->update(['sent_at' => now(), 'status' => 'sent']);
                $sent++;
            } else {
                $item->update(['status' => 'failed', 'error_message' => $result->error ?? 'Provider rejected']);
                $failed++;
            }
        }

        $this->info("Queue processed: {$sent} sent, {$failed} failed.");
        return Command::SUCCESS;
    }
}
