<?php

namespace Tests\Feature\Modules;

use Illuminate\Support\Facades\Artisan;
use Tests\ModuleTestCase;

class RecurringAppointmentTest extends ModuleTestCase
{
    public function test_command_runs()
    {
        $exitCode = Artisan::call('appointments:generate-recurring');
        $this->assertEquals(0, $exitCode);
    }
}
