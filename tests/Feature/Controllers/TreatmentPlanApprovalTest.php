<?php

namespace Tests\Feature\Controllers;

use App\Models\TreatmentPlan;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class TreatmentPlanApprovalTest extends ModuleTestCase
{
    public function test_approve_plan()
    {
        $this->loginAs('veterinario');
        $plan = TreatmentPlan::factory()->create(['status' => 'pending']);
        $response = $this->put(route('treatment-plans.approve', $plan));
        $response->assertRedirect();
        $this->assertEquals('approved', $plan->fresh()->status);
        $this->assertNotNull($plan->fresh()->client_approved_at);
    }

    public function test_reject_plan()
    {
        $this->loginAs('veterinario');
        $plan = TreatmentPlan::factory()->create(['status' => 'pending']);
        $response = $this->put(route('treatment-plans.reject', $plan), [
            'rejection_reason' => 'Valor muito alto',
        ]);
        $response->assertRedirect();
        $this->assertEquals('rejected', $plan->fresh()->status);
        $this->assertEquals('Valor muito alto', $plan->fresh()->rejection_reason);
    }

    public function test_model_approve_method()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'pending']);
        $plan->approve();
        $this->assertTrue($plan->isApproved());
        $this->assertNotNull($plan->client_approved_at);
    }

    public function test_model_reject_method()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'pending']);
        $plan->reject('Cliente desistiu');
        $this->assertTrue($plan->fresh()->isRejected());
        $this->assertEquals('Cliente desistiu', $plan->fresh()->rejection_reason);
    }

    public function test_scopes()
    {
        TreatmentPlan::factory()->count(2)->create(['status' => 'pending']);
        TreatmentPlan::factory()->create(['status' => 'approved']);
        TreatmentPlan::factory()->create(['status' => 'rejected']);

        $this->assertCount(2, TreatmentPlan::pending()->get());
        $this->assertCount(1, TreatmentPlan::approved()->get());
        $this->assertCount(1, TreatmentPlan::rejected()->get());
    }
}
