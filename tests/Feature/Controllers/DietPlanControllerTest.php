<?php

namespace Tests\Feature\Controllers;

use App\Models\DietPlan;
use App\Models\Pet;
use Tests\ModuleTestCase;

class DietPlanControllerTest extends ModuleTestCase
{
    public function test_index()
    {
        $this->loginAs('veterinario');
        DietPlan::factory()->count(3)->create();
        $response = $this->get(route('diet-plans.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $this->loginAs('veterinario');
        $response = $this->get(route('diet-plans.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $this->loginAs('veterinario');
        $pet = Pet::factory()->create();
        $response = $this->post(route('diet-plans.store'), [
            'pet_id' => $pet->id,
            'diet_type' => 'renal',
            'brand' => 'Royal Canin',
            'daily_amount' => '100g',
            'duration_days' => 60,
        ]);
        $response->assertRedirect(route('diet-plans.index'));
        $this->assertDatabaseHas('diet_plans', ['pet_id' => $pet->id]);
    }

    public function test_store_validates_required_fields()
    {
        $this->loginAs('veterinario');
        $response = $this->post(route('diet-plans.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'diet_type']);
    }

    public function test_show()
    {
        $this->loginAs('veterinario');
        $plan = DietPlan::factory()->create();
        $response = $this->get(route('diet-plans.show', $plan));
        $response->assertOk();
    }

    public function test_destroy()
    {
        $this->loginAs('veterinario');
        $plan = DietPlan::factory()->create();
        $response = $this->delete(route('diet-plans.destroy', $plan));
        $response->assertRedirect(route('diet-plans.index'));
        $this->assertDatabaseMissing('diet_plans', ['id' => $plan->id]);
    }
}
