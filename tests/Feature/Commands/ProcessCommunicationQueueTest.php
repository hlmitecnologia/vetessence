<?php

namespace Tests\Feature\Commands;

use App\Models\CommunicationQueue;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProcessCommunicationQueueTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_processes_pending_queue_items()
    {
        CommunicationQueue::factory()->create([
            'channel' => 'email',
            'status' => 'pending',
            'scheduled_at' => now()->subHour(),
        ]);

        $this->artisan('queue:process')
            ->assertExitCode(0);
    }

    public function test_command_with_custom_limit()
    {
        CommunicationQueue::factory()->count(5)->create([
            'channel' => 'email',
            'status' => 'pending',
            'scheduled_at' => now()->subHour(),
        ]);

        $this->artisan('queue:process', ['--limit' => 3])
            ->assertExitCode(0);
    }
}
