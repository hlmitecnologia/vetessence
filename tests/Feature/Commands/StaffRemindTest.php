<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StaffRemindTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_sends_shift_reminders()
    {
        $this->artisan('staff:remind')
            ->assertExitCode(0);
    }

    public function test_command_with_custom_days_option()
    {
        $this->artisan('staff:remind', ['--days' => 3])
            ->assertExitCode(0);
    }
}
