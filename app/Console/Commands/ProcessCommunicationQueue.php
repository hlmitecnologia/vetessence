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
            $this->info('Nenhum item pendente na fila.');
            return Command::SUCCESS;
        }

        foreach ($items as $item) {
            $channel = NotificationChannel::tryFrom($item->channel);

            if (!$channel) {
                $item->update(['status' => 'failed', 'error_message' => 'Canal não suportado']);
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
                $item->update(['status' => 'failed', 'error_message' => $result->error ?? 'Provedor rejeitou']);
                $failed++;
            }
        }

        $this->info("Fila processada: {$sent} enviados, {$failed} falhas.");
        return Command::SUCCESS;
    }
}
