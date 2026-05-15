<?php

namespace Tests\Feature\Controllers;

use Tests\ModuleTestCase;

class StaffScheduleControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('staff-schedules.index'));
        $response->assertOk();
    }

    public function test_on_call_calendar()
    {
        $response = $this->get(route('staff-schedules.on-call-calendar'));
        $response->assertOk();
    }
}
