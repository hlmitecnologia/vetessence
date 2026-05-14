<?php

namespace Tests\Feature\Services;

use App\Models\DrugInteraction;
use App\Services\DrugInteractionService;
use Tests\ModuleTestCase;

class DrugInteractionServiceTest extends ModuleTestCase
{
    private DrugInteractionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DrugInteractionService::class);
    }

    public function test_check_detects_interaction()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'Cetoprofeno', 'drug_b' => 'Meloxicam',
        ]);

        $result = $this->service->check(['Cetoprofeno', 'Meloxicam']);
        $this->assertTrue($result->isNotEmpty());
    }

    public function test_check_returns_empty_with_no_interaction()
    {
        $result = $this->service->check(['Dipirona', 'Amoxicilina']);
        $this->assertTrue($result->isEmpty());
    }

    public function test_check_handles_case_insensitive()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'cetoprofeno', 'drug_b' => 'meloxicam',
        ]);

        $result = $this->service->check(['CETOPROFENO', 'MELOXICAM']);
        $this->assertTrue($result->isNotEmpty());
    }

    public function test_check_handles_reversed_order()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'A', 'drug_b' => 'B',
        ]);

        $result = $this->service->check(['B', 'A']);
        $this->assertTrue($result->isNotEmpty());
    }

    public function test_check_ignores_duplicates()
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'A', 'drug_b' => 'B',
        ]);

        $result = $this->service->check(['A', 'B', 'A']);
        $this->assertCount(1, $result);
    }

    public function test_for_drug_returns_all_interactions()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'C']);

        $result = $this->service->forDrug('A');
        $this->assertCount(2, $result);
    }

    public function test_between_returns_interaction()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);

        $result = $this->service->between('A', 'B');
        $this->assertNotNull($result);
    }

    public function test_between_returns_null_for_no_interaction()
    {
        $result = $this->service->between('A', 'Z');
        $this->assertNull($result);
    }

    public function test_between_handles_reversed_args()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);

        $result = $this->service->between('B', 'A');
        $this->assertNotNull($result);
    }
}
