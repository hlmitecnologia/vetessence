<?php

namespace Tests\Feature\Controllers;

use App\Models\DrugInteraction;
use Tests\ModuleTestCase;

class DrugInteractionControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        DrugInteraction::factory()->count(3)->create();
        $response = $this->get(route('drug-interactions.index'));
        $response->assertOk();
    }

    public function test_index_with_search()
    {
        DrugInteraction::factory()->create(['drug_a' => 'Cetoprofeno']);
        DrugInteraction::factory()->create(['drug_a' => 'Meloxicam']);

        $response = $this->get(route('drug-interactions.index', ['search' => 'Cetoprofeno']));
        $response->assertOk();
    }

    public function test_index_filters_by_severity()
    {
        DrugInteraction::factory()->create(['severity' => 'contraindicated']);
        DrugInteraction::factory()->create(['severity' => 'caution']);

        $response = $this->get(route('drug-interactions.index', ['severity' => 'contraindicated']));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
            'severity' => 'contraindicated',
            'description' => 'Alto risco de úlcera gástrica.',
            'mechanism' => 'Inibição de COX-1 e COX-2',
            'management' => 'Não administrar juntos.',
            'source' => 'Veterinary Drug Handbook',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('drug-interactions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('drug_interactions', [
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
            'severity' => 'contraindicated',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('drug-interactions.store'), []);
        $response->assertSessionHasErrors(['drug_a', 'drug_b', 'severity', 'description']);
    }

    public function test_store_validates_different_drugs()
    {
        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Cetoprofeno',
            'severity' => 'caution',
            'description' => 'Same drug.',
        ]);
        $response->assertSessionHasErrors('drug_b');
    }

    public function test_store_prevents_duplicate()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
        ]);

        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
            'severity' => 'contraindicated',
            'description' => 'Duplicate test.',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_store_prevents_reversed_duplicate()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'Meloxicam',
            'drug_b' => 'Cetoprofeno',
        ]);

        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
            'severity' => 'contraindicated',
            'description' => 'Reversed duplicate test.',
        ]);
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_show()
    {
        $interaction = DrugInteraction::factory()->create();
        $response = $this->get(route('drug-interactions.show', $interaction));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $interaction = DrugInteraction::factory()->create([
            'severity' => 'caution',
        ]);

        $response = $this->put(route('drug-interactions.update', $interaction), [
            'drug_a' => $interaction->drug_a,
            'drug_b' => $interaction->drug_b,
            'severity' => 'contraindicated',
            'description' => $interaction->description,
            'mechanism' => $interaction->mechanism,
            'management' => 'Atualizado: Não administrar.',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('drug-interactions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('drug_interactions', [
            'id' => $interaction->id,
            'severity' => 'contraindicated',
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $interaction = DrugInteraction::factory()->create();
        $response = $this->delete(route('drug-interactions.destroy', $interaction));
        $response->assertRedirect(route('drug-interactions.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('drug_interactions', ['id' => $interaction->id]);
    }

    public function test_check_api_returns_interactions()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
            'severity' => 'contraindicated',
        ]);

        $response = $this->post(route('drug-interactions.check'), [
            'drugs' => ['Cetoprofeno', 'Meloxicam'],
        ]);
        $response->assertOk();
        $response->assertJsonStructure([
            'has_interactions',
            'interactions' => [
                '*' => ['drug_a', 'drug_b', 'severity', 'description', 'management'],
            ],
        ]);
        $response->assertJson(['has_interactions' => true]);
    }

    public function test_check_api_returns_empty_for_no_interactions()
    {
        $response = $this->post(route('drug-interactions.check'), [
            'drugs' => ['Amoxicilina', 'Dipirona'],
        ]);
        $response->assertOk();
        $response->assertJson(['has_interactions' => false, 'interactions' => []]);
    }

    public function test_check_api_validates_min_two_drugs()
    {
        $response = $this->post(route('drug-interactions.check'), [
            'drugs' => ['Amoxicilina'],
        ]);
        $response->assertSessionHasErrors('drugs');
    }
}
