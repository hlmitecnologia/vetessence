<?php

namespace Tests\Feature\Modules;

use Tests\ModuleTestCase;

class PatientFlowBoardTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_flow_board()
    {
        $response = $this->get(route('appointments.flow-board'));
        $response->assertOk();
    }
}
