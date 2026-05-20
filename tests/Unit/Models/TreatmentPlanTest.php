<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\TreatmentPlan;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TreatmentPlanTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        TreatmentPlan::create([
            'plan_number' => 'PLN-2026-00001', 'pet_id' => $pet->id,
            'tutor_id' => $tutor->id, 'vet_id' => $vet->id,
            'title' => 'Tratamento', 'status' => 'pending_approval',
        ]);
        $this->assertDatabaseHas('treatment_plans', ['plan_number' => 'PLN-2026-00001', 'title' => 'Tratamento']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-001', 'pet_id' => $pet->id, 'title' => 'Teste', 'status' => 'pending_approval']);
        $this->assertInstanceOf(Pet::class, $tp->pet);
    }

    public function test_tutor_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-002', 'pet_id' => $pet->id, 'tutor_id' => $tutor->id, 'title' => 'Teste', 'status' => 'pending_approval']);
        $this->assertInstanceOf(Tutor::class, $tp->tutor);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $tp = TreatmentPlan::create(['plan_number' => 'PLN-003', 'pet_id' => $pet->id, 'vet_id' => $vet->id, 'title' => 'Teste', 'status' => 'pending_approval']);
        $this->assertInstanceOf(User::class, $tp->vet);
    }

    public function test_pending_scope()
    {
        TreatmentPlan::factory()->create(['status' => 'pending_approval']);
        TreatmentPlan::factory()->create(['status' => 'approved']);
        $this->assertEquals(1, TreatmentPlan::pending()->count());
    }

    public function test_approved_scope()
    {
        TreatmentPlan::factory()->create(['status' => 'pending_approval']);
        TreatmentPlan::factory()->create(['status' => 'approved']);
        $this->assertEquals(1, TreatmentPlan::approved()->count());
    }

    public function test_rejected_scope()
    {
        TreatmentPlan::factory()->create(['status' => 'pending_approval']);
        TreatmentPlan::factory()->create(['status' => 'rejected']);
        $this->assertEquals(1, TreatmentPlan::rejected()->count());
    }

    public function test_approve_method()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'pending_approval']);
        $plan->approve();
        $this->assertEquals('approved', $plan->fresh()->status);
        $this->assertNotNull($plan->fresh()->client_approved_at);
    }

    public function test_reject_method()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'pending_approval']);
        $plan->reject('Orçamento muito alto');
        $this->assertEquals('rejected', $plan->fresh()->status);
        $this->assertNotNull($plan->fresh()->rejected_at);
        $this->assertEquals('Orçamento muito alto', $plan->fresh()->rejection_reason);
    }

    public function test_is_pending()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'pending_approval']);
        $this->assertTrue($plan->isPending());
        $this->assertFalse($plan->isApproved());
    }

    public function test_is_approved()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'approved']);
        $this->assertTrue($plan->isApproved());
    }

    public function test_is_draft()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'draft']);
        $this->assertTrue($plan->isDraft());
    }

    public function test_is_completed()
    {
        $plan = TreatmentPlan::factory()->create(['status' => 'completed']);
        $this->assertTrue($plan->isCompleted());
    }
}
