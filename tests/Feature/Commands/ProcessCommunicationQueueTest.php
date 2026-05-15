<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProcessCommunicationQueueTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_processes_pending_queue_items()
    {
        $this->artisan('queue:process')
            ->assertExitCode(0);
    }

    public function test_command_with_custom_limit()
    {
        $this->artisan('queue:process', ['--limit' => 100])
            ->assertExitCode(0);
    }
}
