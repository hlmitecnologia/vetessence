<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ProcessBirthdayCampaignsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_processes_birthdays()
    {
        $this->artisan('birthday:process')
            ->assertExitCode(0);
    }
}
