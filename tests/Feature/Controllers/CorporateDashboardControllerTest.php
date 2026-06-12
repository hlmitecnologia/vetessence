<?php

namespace Tests\Feature\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\Invoice;
use App\Models\Pet;
use Tests\ModuleTestCase;

class CorporateDashboardControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        Branch::factory()->count(2)->create();

        $response = $this->get(route('corporate-dashboard.index'));

        $response->assertOk();
    }

    public function test_index_without_branches()
    {
        $response = $this->get(route('corporate-dashboard.index'));

        $response->assertOk();
    }

    public function test_index_passes_branches_to_view()
    {
        Branch::factory()->create(['name' => 'Matriz']);

        $response = $this->get(route('corporate-dashboard.index'));

        $response->assertOk();
        $response->assertSee('Matriz');
    }
}
