<?php

namespace Tests\Feature\Modules;

use App\Models\DrugInteraction;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class DrugInteractionTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('drug-interactions.index'));
        $response->assertOk();
    }

    public function test_store_creates_interaction()
    {
        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'Cetoprofeno',
            'drug_b' => 'Meloxicam',
            'severity' => 'contraindicated',
            'description' => 'Risco de ulceração gástrica',
            'mechanism' => 'Inibição COX dupla',
            'category' => 'AINE',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('drug-interactions.index'));
        $this->assertDatabaseHas('drug_interactions', ['drug_a' => 'Cetoprofeno', 'drug_b' => 'Meloxicam']);
    }

    public function test_rejects_duplicate_a_b()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);
        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'A', 'drug_b' => 'B',
            'severity' => 'caution', 'description' => 'test',
        ]);
        $response->assertSessionHas('error');
    }

    public function test_rejects_duplicate_b_a()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);
        $response = $this->post(route('drug-interactions.store'), [
            'drug_a' => 'B', 'drug_b' => 'A',
            'severity' => 'caution', 'description' => 'test',
        ]);
        $response->assertSessionHas('error');
    }

    public function test_service_check_detects_conflict()
    {
        DrugInteraction::factory()->create(['drug_a' => 'Cetoprofeno', 'drug_b' => 'Meloxicam']);
        $service = app(\App\Services\DrugInteractionService::class);
        $result = $service->check(['Cetoprofeno', 'Meloxicam']);
        $this->assertTrue($result->isNotEmpty());
    }

    public function test_service_check_no_conflict()
    {
        $result = app(\App\Services\DrugInteractionService::class)->check(['Dipirona', 'Amoxicilina']);
        $this->assertTrue($result->isEmpty());
    }

    public function test_check_api_endpoint()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);
        $response = $this->postJson(route('drug-interactions.check'), [
            'drugs' => ['A', 'B'],
        ]);
        $response->assertOk()->assertJson(['has_interactions' => true]);
    }
}
