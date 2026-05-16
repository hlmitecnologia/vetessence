<?php

namespace Tests\Feature;

use Tests\ModuleTestCase;

class ScannerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_scanner_page_loads()
    {
        $response = $this->get(route('scanner.index'));
        $response->assertOk();
    }
}
