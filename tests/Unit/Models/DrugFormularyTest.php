<?php

namespace Tests\Unit\Models;

use App\Models\DrugFormulary;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DrugFormularyTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        DrugFormulary::create([
            'drug' => 'Meloxicam',
            'species' => 'Canina',
            'dosage_mg_kg' => 0.2,
            'max_dose' => 15.00,
            'route' => 'SC',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('drug_formulary', [
            'drug' => 'Meloxicam',
            'route' => 'SC',
            'is_active' => true,
        ]);
    }

    public function test_dosage_cast()
    {
        $drug = DrugFormulary::factory()->create(['dosage_mg_kg' => 0.20]);
        $this->assertEquals('0.20', $drug->dosage_mg_kg);
    }

    public function test_active_scope()
    {
        DrugFormulary::factory()->create(['is_active' => true]);
        DrugFormulary::factory()->create(['is_active' => false]);
        $this->assertEquals(1, DrugFormulary::active()->count());
    }

    public function test_for_species_scope()
    {
        DrugFormulary::factory()->create(['species' => 'Canina']);
        DrugFormulary::factory()->create(['species' => 'Felina']);
        $this->assertEquals(1, DrugFormulary::forSpecies('Canina')->count());
    }

    public function test_calculate_dose()
    {
        $drug = DrugFormulary::create([
            'drug' => 'Meloxicam',
            'species' => 'Canina',
            'dosage_mg_kg' => 0.2,
            'max_dose' => 15.00,
            'route' => 'SC',
            'is_active' => true,
        ]);
        $result = DrugFormulary::calculateDose($drug->id, 30, 'Canina');
        $this->assertNotNull($result);
        $this->assertEquals(6.00, $result['calculated_dose_mg']);
    }

    public function test_calculate_dose_respects_max_dose()
    {
        $drug = DrugFormulary::create([
            'drug' => 'Meloxicam',
            'species' => 'Canina',
            'dosage_mg_kg' => 0.2,
            'max_dose' => 5.00,
            'route' => 'SC',
            'is_active' => true,
        ]);
        $result = DrugFormulary::calculateDose($drug->id, 30, 'Canina');
        $this->assertEquals(5.00, $result['calculated_dose_mg']);
    }

    public function test_calculate_dose_returns_null_for_wrong_species()
    {
        $drug = DrugFormulary::create([
            'drug' => 'Meloxicam',
            'species' => 'Canina',
            'dosage_mg_kg' => 0.2,
            'route' => 'SC',
            'is_active' => true,
        ]);
        $this->assertNull(DrugFormulary::calculateDose($drug->id, 10, 'Felina'));
    }
}
