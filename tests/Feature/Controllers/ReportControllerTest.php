<?php

namespace Tests\Feature\Controllers;

use Tests\ModuleTestCase;

class ReportControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_financial_returns_200()
    {
        $response = $this->get(route('reports.financial'));
        $response->assertOk();
    }

    public function test_export_pdf_returns_200()
    {
        $response = $this->get(route('reports.export'));
        $response->assertOk();
    }
}
