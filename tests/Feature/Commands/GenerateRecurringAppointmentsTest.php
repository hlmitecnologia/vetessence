<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class GenerateRecurringAppointmentsTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_generates_recurring_appointments()
    {
        $this->artisan('appointments:generate-recurring')
            ->assertExitCode(0);
    }
}
