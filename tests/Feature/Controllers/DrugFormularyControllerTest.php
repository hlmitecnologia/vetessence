<?php

namespace Tests\Feature\Controllers;

use App\Models\DrugFormulary;
use Tests\ModuleTestCase;

class DrugFormularyControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        DrugFormulary::factory()->count(3)->create();

        $response = $this->get(route('drug-formulary.index'));

        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('drug-formulary.store'), [
            'drug' => 'Amoxicilina',
            'species' => 'Canina',
            'dosage_mg_kg' => 10.0,
            'max_dose' => 500,
            'route' => 'VO',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('drug-formulary.index'));
        $this->assertDatabaseHas('drug_formulary', ['drug' => 'Amoxicilina']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('drug-formulary.store'), []);

        $response->assertSessionHasErrors(['drug', 'species', 'dosage_mg_kg']);
    }

    public function test_update()
    {
        $formulary = DrugFormulary::factory()->create();

        $response = $this->put(route('drug-formulary.update', $formulary), [
            'drug' => 'Amoxicilina 500mg',
            'species' => 'Canina',
            'dosage_mg_kg' => 15.0,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('drug-formulary.index'));
        $this->assertDatabaseHas('drug_formulary', [
            'id' => $formulary->id,
            'drug' => 'Amoxicilina 500mg',
        ]);
    }

    public function test_destroy()
    {
        $formulary = DrugFormulary::factory()->create();

        $response = $this->delete(route('drug-formulary.destroy', $formulary));

        $response->assertRedirect(route('drug-formulary.index'));
        $this->assertDatabaseMissing('drug_formulary', ['id' => $formulary->id]);
    }

    public function test_calculate_returns_dose()
    {
        $formulary = DrugFormulary::factory()->create(['species' => 'Canina']);

        $response = $this->post(route('drug-formulary.calculate'), [
            'drug_formulary_id' => $formulary->id,
            'weight_kg' => 20,
            'species' => 'Canina',
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'drug', 'species', 'weight_kg', 'dosage_mg_kg', 'calculated_dose_mg', 'max_dose', 'route',
        ]);
    }

    public function test_calculate_returns_404_for_mismatched_species()
    {
        $formulary = DrugFormulary::factory()->create(['species' => 'Felina']);

        $response = $this->post(route('drug-formulary.calculate'), [
            'drug_formulary_id' => $formulary->id,
            'weight_kg' => 20,
            'species' => 'Equina',
        ]);

        $response->assertNotFound();
    }
}
