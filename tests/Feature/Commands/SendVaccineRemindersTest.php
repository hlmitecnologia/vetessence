<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SendVaccineRemindersTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_sends_reminders_for_upcoming_vaccines()
    {
        $this->artisan('vaccines:remind')
            ->assertExitCode(0);
    }

    public function test_command_with_custom_days_option()
    {
        $this->artisan('vaccines:remind', ['--days' => 3])
            ->assertExitCode(0);
    }
}
