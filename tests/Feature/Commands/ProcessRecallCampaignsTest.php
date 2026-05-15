<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProcessRecallCampaignsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_processes_recall_campaigns()
    {
        $this->artisan('recall:process')
            ->assertExitCode(0);
    }
}
