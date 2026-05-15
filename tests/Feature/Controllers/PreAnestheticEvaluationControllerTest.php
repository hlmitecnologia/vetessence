<?php

namespace Tests\Feature\Controllers;

use App\Models\PreAnestheticEvaluation;
use App\Models\Pet;
use Tests\ModuleTestCase;

class PreAnestheticEvaluationControllerTest extends ModuleTestCase
{
    public function test_index()
    {
        $this->loginAs('veterinario');
        PreAnestheticEvaluation::factory()->count(3)->create();
        $response = $this->get(route('pre-anesthetic-evaluations.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $this->loginAs('veterinario');
        $response = $this->get(route('pre-anesthetic-evaluations.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $this->loginAs('veterinario');
        $pet = Pet::factory()->create();
        $response = $this->post(route('pre-anesthetic-evaluations.store'), [
            'pet_id' => $pet->id,
            'asa_score' => 2,
            'fasted' => true,
            'hydrated' => true,
            'status' => 'pending',
        ]);
        $response->assertRedirect(route('pre-anesthetic-evaluations.index'));
        $this->assertDatabaseHas('pre_anesthetic_evaluations', ['pet_id' => $pet->id]);
    }

    public function test_store_validates_required_fields()
    {
        $this->loginAs('veterinario');
        $response = $this->post(route('pre-anesthetic-evaluations.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'asa_score', 'status']);
    }

    public function test_show()
    {
        $this->loginAs('veterinario');
        $eval = PreAnestheticEvaluation::factory()->create();
        $response = $this->get(route('pre-anesthetic-evaluations.show', $eval));
        $response->assertOk();
    }

    public function test_destroy()
    {
        $this->loginAs('veterinario');
        $eval = PreAnestheticEvaluation::factory()->create();
        $response = $this->delete(route('pre-anesthetic-evaluations.destroy', $eval));
        $response->assertRedirect(route('pre-anesthetic-evaluations.index'));
        $this->assertDatabaseMissing('pre_anesthetic_evaluations', ['id' => $eval->id]);
    }
}
