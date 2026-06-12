<?php

namespace Tests\Feature\Livewire;

use App\Models\DrugInteraction;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class DrugInteractionFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        Livewire::test('drug-interaction-form')
            ->set('drug_a', 'Cetoprofeno')
            ->set('drug_b', 'Meloxicam')
            ->set('severity', 'contraindicated')
            ->set('description', 'AINEs concomitantes aumentam risco de ulceração gástrica.')
            ->call('save')
            ->assertDispatched('drug-interaction-saved');

        $this->assertDatabaseHas('drug_interactions', ['drug_a' => 'Cetoprofeno']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('drug-interaction-form')
            ->set('severity', '')
            ->call('save')
            ->assertHasErrors(['drug_a', 'drug_b', 'severity', 'description']);
    }

    public function test_validates_different_drugs(): void
    {
        Livewire::test('drug-interaction-form')
            ->set('drug_a', 'Mesmo Medicamento')
            ->set('drug_b', 'Mesmo Medicamento')
            ->set('severity', 'caution')
            ->set('description', 'Teste de validação')
            ->call('save')
            ->assertHasErrors(['drug_b']);
    }

    public function test_can_edit(): void
    {
        $interaction = DrugInteraction::factory()->create();

        Livewire::test('drug-interaction-form')
            ->dispatch('editDrugInteraction', id: $interaction->id)
            ->assertSet('drug_a', $interaction->drug_a)
            ->assertSet('drug_b', $interaction->drug_b)
            ->set('severity', 'minor')
            ->call('save')
            ->assertDispatched('drug-interaction-saved');

        $this->assertDatabaseHas('drug_interactions', ['id' => $interaction->id, 'severity' => 'minor']);
    }

    public function test_reset_form(): void
    {
        Livewire::test('drug-interaction-form')
            ->set('drug_a', 'A')
            ->set('drug_b', 'B')
            ->set('description', 'Desc')
            ->dispatch('resetForm')
            ->assertSet('drug_a', '')
            ->assertSet('drug_b', '')
            ->assertSet('severity', 'caution')
            ->assertSet('description', '')
            ->assertSet('is_active', true);
    }

    public function test_handles_duplicate_interaction(): void
    {
        DrugInteraction::factory()->create([
            'drug_a' => 'Furosemida',
            'drug_b' => 'Enalapril',
        ]);

        Livewire::test('drug-interaction-form')
            ->set('drug_a', 'Furosemida')
            ->set('drug_b', 'Enalapril')
            ->set('severity', 'caution')
            ->set('description', 'Interação duplicada')
            ->call('save')
            ->assertNotDispatched('drug-interaction-saved');
    }
}
