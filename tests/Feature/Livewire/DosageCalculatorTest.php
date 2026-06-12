<?php

namespace Tests\Feature\Livewire;

use App\Models\DrugFormulary;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class DosageCalculatorTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_mounts_with_drug_list()
    {
        DrugFormulary::factory()->count(3)->create();

        Livewire::test('dosage-calculator')
            ->assertSet('drugs', function ($drugs) {
                return $drugs->count() === 3;
            });
    }

    public function test_calculate_with_valid_drug_returns_result()
    {
        $drug = DrugFormulary::factory()->create([
            'species' => 'Canina',
            'dosage_mg_kg' => 2.5,
            'max_dose' => 100,
            'route' => 'VO',
        ]);

        Livewire::test('dosage-calculator')
            ->set('selectedDrugId', $drug->id)
            ->set('weightKg', 10)
            ->set('species', 'Canina')
            ->call('calculate')
            ->assertSet('result', function ($result) {
                return is_array($result) && $result['calculated_dose_mg'] === 25.0;
            })
            ->assertSet('error', null);
    }

    public function test_calculate_with_invalid_drug_returns_error()
    {
        DrugFormulary::factory()->create([
            'species' => 'Felina',
            'dosage_mg_kg' => 1.0,
        ]);

        Livewire::test('dosage-calculator')
            ->set('selectedDrugId', 999)
            ->set('weightKg', 10)
            ->set('species', 'Equina')
            ->call('calculate')
            ->assertSet('error', 'Nenhuma dosagem encontrada para este fármaco/espécie.')
            ->assertSet('result', null);
    }
}
