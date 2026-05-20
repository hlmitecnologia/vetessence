<?php

namespace Tests\Feature\Commands;

use App\Models\Appointment;
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

    public function test_command_runs_with_existing_recurring()
    {
        Appointment::factory()->create(['is_recurring' => true]);

        $this->artisan('appointments:generate-recurring')
            ->assertExitCode(0);
    }
}
