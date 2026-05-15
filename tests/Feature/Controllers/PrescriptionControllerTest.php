<?php

namespace Tests\Feature\Controllers;

use Tests\ModuleTestCase;

class PrescriptionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('prescriptions.index'));
        $response->assertOk();
    }
}
