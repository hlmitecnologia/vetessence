<?php

namespace Tests\Feature\Controllers;

use App\Models\CommissionLog;
use App\Models\CommissionRate;
use App\Models\Invoice;
use App\Models\Service;
use App\Models\User;
use Tests\ModuleTestCase;

class CommissionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        CommissionLog::factory()->create();
        $response = $this->get(route('commissions.index'));
        $response->assertOk();
    }

    public function test_show()
    {
        $log = CommissionLog::factory()->create();
        $response = $this->get(route('commissions.show', $log));
        $response->assertOk();
    }

    public function test_mark_paid()
    {
        $log = CommissionLog::factory()->create(['status' => 'pending']);
        $response = $this->post(route('commissions.mark-paid', $log));
        $response->assertRedirect();
        $this->assertEquals('paid', $log->fresh()->status);
    }

    public function test_rates_page()
    {
        $response = $this->get(route('commissions.rates'));
        $response->assertOk();
    }

    public function test_rates_store()
    {
        $vet = User::factory()->create();
        $service = Service::factory()->create();

        $response = $this->post(route('commissions.rates-store'), [
            'user_id' => $vet->id,
            'commissionable_type' => 'service',
            'commissionable_id' => $service->id,
            'rate_type' => 'percentage',
            'rate_value' => 10,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('commission_rates', ['user_id' => $vet->id, 'rate_value' => 10]);
    }

    public function test_rates_destroy()
    {
        $rate = CommissionRate::factory()->create();
        $response = $this->delete(route('commissions.rates-destroy', $rate));
        $response->assertRedirect();
        $this->assertDatabaseMissing('commission_rates', ['id' => $rate->id]);
    }
}
