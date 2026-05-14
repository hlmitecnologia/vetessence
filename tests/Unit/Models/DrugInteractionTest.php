<?php

namespace Tests\Unit\Models;

use App\Models\DrugInteraction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DrugInteractionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_active_scope()
    {
        DrugInteraction::factory()->create(['is_active' => true]);
        DrugInteraction::factory()->create(['is_active' => false]);

        $this->assertCount(1, DrugInteraction::active()->get());
    }

    public function test_for_drug_scope()
    {
        DrugInteraction::factory()->create(['drug_a' => 'A', 'drug_b' => 'B']);
        DrugInteraction::factory()->create(['drug_a' => 'C', 'drug_b' => 'A']);

        $this->assertCount(2, DrugInteraction::forDrug('A')->get());
    }

    public function test_by_severity_scope()
    {
        DrugInteraction::factory()->create(['severity' => 'contraindicated']);
        DrugInteraction::factory()->create(['severity' => 'caution']);

        $this->assertCount(1, DrugInteraction::bySeverity('contraindicated')->get());
    }
}
