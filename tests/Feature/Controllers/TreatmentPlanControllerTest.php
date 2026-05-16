<?php

namespace Tests\Feature\Controllers;

use App\Models\Pet;
use App\Models\TreatmentPlan;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class TreatmentPlanControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('treatment-plans.index'));
        $response->assertOk();
    }

    public function test_store_creates_treatment_plan()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $vet = User::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->post(route('treatment-plans.store'), [
            'pet_id' => $pet->id,
            'tutor_id' => $tutor->id,
            'vet_id' => $vet->id,
            'title' => 'Plano de quimioterapia',
            'description' => '6 sessões',
            'status' => 'pending',
            'vet_notes' => 'Aguardando aprovação',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('treatment_plans', [
            'pet_id' => $pet->id,
            'title' => 'Plano de quimioterapia',
        ]);
    }

    public function test_show()
    {
        $plan = TreatmentPlan::factory()->create();

        $response = $this->get(route('treatment-plans.show', $plan));
        $response->assertOk();
    }

    public function test_update()
    {
        $plan = TreatmentPlan::factory()->create();

        $response = $this->put(route('treatment-plans.update', $plan), [
            'pet_id' => $plan->pet_id,
            'tutor_id' => $plan->tutor_id,
            'vet_id' => $plan->vet_id,
            'title' => $plan->title,
            'status' => 'pending',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('treatment_plans', [
            'id' => $plan->id,
            'status' => 'pending',
        ]);
    }

    public function test_destroy()
    {
        $plan = TreatmentPlan::factory()->create();

        $response = $this->delete(route('treatment-plans.destroy', $plan));
        $response->assertRedirect();
        $this->assertDatabaseMissing('treatment_plans', ['id' => $plan->id]);
    }
}
