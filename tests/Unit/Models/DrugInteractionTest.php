<?php

namespace Tests\Unit\Models;

use App\Models\DrugInteraction;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DrugInteractionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        DrugInteraction::create([
            'drug_a' => 'Dipirona',
            'drug_b' => 'Cetoprofeno',
            'severity' => 'contraindicated',
            'description' => 'Risco de hemorragia',
            'mechanism' => 'Inibicao da agregacao plaquetaria',
            'management' => 'Nao administrar juntos',
            'source' => 'Veterinary Drug Handbook',
            'category' => 'nsaids',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('drug_interactions', [
            'drug_a' => 'Dipirona',
            'drug_b' => 'Cetoprofeno',
            'severity' => 'contraindicated',
            'is_active' => true,
        ]);
    }

    public function test_active_scope()
    {
        DrugInteraction::create(['drug_a' => 'A', 'drug_b' => 'B', 'is_active' => true]);
        DrugInteraction::create(['drug_a' => 'C', 'drug_b' => 'D', 'is_active' => false]);

        $this->assertCount(1, DrugInteraction::active()->get());
    }

    public function test_for_drug_scope()
    {
        DrugInteraction::create(['drug_a' => 'A', 'drug_b' => 'B', 'severity' => 'contraindicated']);
        DrugInteraction::create(['drug_a' => 'C', 'drug_b' => 'A', 'severity' => 'caution']);

        $this->assertCount(2, DrugInteraction::forDrug('A')->get());
    }

    public function test_by_severity_scope()
    {
        DrugInteraction::create(['drug_a' => 'A', 'drug_b' => 'B', 'severity' => 'contraindicated']);
        DrugInteraction::create(['drug_a' => 'C', 'drug_b' => 'D', 'severity' => 'caution']);

        $this->assertCount(1, DrugInteraction::bySeverity('contraindicated')->get());
    }
}
