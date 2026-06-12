<?php

namespace Tests\Feature\Livewire;

use App\Models\DrugFormulary;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class DrugFormularyFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        Livewire::test('drug-formulary-form')
            ->set('drug', 'Meloxicam')
            ->set('species', 'Canina')
            ->set('dosage_mg_kg', '0.2')
            ->call('save')
            ->assertDispatched('drug-formulary-saved');

        $this->assertDatabaseHas('drug_formulary', ['drug' => 'Meloxicam']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('drug-formulary-form')
            ->call('save')
            ->assertHasErrors(['drug', 'species', 'dosage_mg_kg']);
    }

    public function test_validates_dosage_numeric(): void
    {
        Livewire::test('drug-formulary-form')
            ->set('drug', 'Dipirona')
            ->set('species', 'Canina')
            ->set('dosage_mg_kg', 'not-a-number')
            ->call('save')
            ->assertHasErrors(['dosage_mg_kg']);
    }

    public function test_can_edit(): void
    {
        $drugFormulary = DrugFormulary::factory()->create(['drug' => 'Amoxicilina']);

        Livewire::test('drug-formulary-form')
            ->dispatch('editDrugFormulary', id: $drugFormulary->id)
            ->assertSet('drug', $drugFormulary->drug)
            ->set('dosage_mg_kg', '15')
            ->call('save')
            ->assertDispatched('drug-formulary-saved');

        $this->assertDatabaseHas('drug_formulary', ['id' => $drugFormulary->id, 'dosage_mg_kg' => 15]);
    }

    public function test_reset_form(): void
    {
        Livewire::test('drug-formulary-form')
            ->set('drug', 'Test')
            ->set('species', 'Felina')
            ->dispatch('resetForm')
            ->assertSet('drug', '')
            ->assertSet('species', '')
            ->assertSet('dosage_mg_kg', '')
            ->assertSet('is_active', true);
    }
}
