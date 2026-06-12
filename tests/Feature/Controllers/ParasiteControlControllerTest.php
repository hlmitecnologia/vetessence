<?php

namespace Tests\Feature\Controllers;

use App\Models\ParasiteControl;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class ParasiteControlControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        ParasiteControl::factory()->count(3)->create();
        $response = $this->get(route('parasite-controls.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_pet()
    {
        $pet = Pet::factory()->create();
        ParasiteControl::factory()->create(['pet_id' => $pet->id]);
        ParasiteControl::factory()->count(2)->create();
        $response = $this->get(route('parasite-controls.index', ['pet_id' => $pet->id]));
        $response->assertOk();
    }

    public function test_create()
    {
        Pet::factory()->count(2)->create();
        User::factory()->count(2)->create();
        $response = $this->get(route('parasite-controls.create'));
        $response->assertOk();
    }

    public function test_store_creates_control()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $response = $this->post(route('parasite-controls.store'), [
            'pet_id' => $pet->id,
            'product_name' => 'NexGard',
            'active_ingredient' => 'Afoxolaner',
            'type' => 'flea',
            'application_date' => now()->format('Y-m-d'),
            'next_due_date' => now()->addMonths(3)->format('Y-m-d'),
            'dose' => '1 comprimido',
            'batch' => 'B123',
            'vet_id' => $vet->id,
            'notes' => 'Aplicação mensal',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('parasite_controls', [
            'product_name' => 'NexGard',
            'pet_id' => $pet->id,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('parasite-controls.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'product_name', 'type', 'application_date', 'vet_id']);
    }

    public function test_show()
    {
        $control = ParasiteControl::factory()->create();
        $response = $this->get(route('parasite-controls.show', $control));
        $response->assertOk();
    }

    public function test_edit()
    {
        $control = ParasiteControl::factory()->create();
        $response = $this->get(route('parasite-controls.edit', $control));
        $response->assertOk();
    }

    public function test_update_modifies_control()
    {
        $control = ParasiteControl::factory()->create(['product_name' => 'Old']);
        $response = $this->put(route('parasite-controls.update', $control), [
            'pet_id' => $control->pet_id,
            'product_name' => 'Updated Product',
            'type' => 'tick',
            'application_date' => now()->format('Y-m-d'),
            'vet_id' => $control->vet_id,
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('parasite_controls', [
            'id' => $control->id,
            'product_name' => 'Updated Product',
        ]);
    }

    public function test_destroy_deletes_control()
    {
        $control = ParasiteControl::factory()->create();
        $response = $this->delete(route('parasite-controls.destroy', $control));
        $response->assertRedirect();
        $this->assertDatabaseMissing('parasite_controls', ['id' => $control->id]);
    }
}
