<?php

namespace Tests\Feature\Modules;

use Tests\ModuleTestCase;

class ControlledSubstanceReportTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_monthly_report()
    {
        $response = $this->get(route('controlled-substances.reports.monthly'));
        $response->assertOk();
    }

    public function test_annual_report()
    {
        $response = $this->get(route('controlled-substances.reports.annual'));
        $response->assertOk();
    }

    public function test_export_csv()
    {
        $response = $this->get(route('controlled-substances.reports.export-csv'));
        $response->assertOk();
    }
}
