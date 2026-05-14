<?php

namespace Tests\Feature\Modules;

use App\Models\ClinicalReportTemplate;
use Tests\ModuleTestCase;

class ClinicalReportTemplateTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('clinical-report-templates.index'));
        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('clinical-report-templates.store'), [
            'name' => 'SOAP Canino Geral',
            'species' => 'canine',
            'specialty' => 'Clínica Geral',
            'category' => 'SOAP',
            'content' => '**Queixa Principal:** {{queixa}}',
            'is_active' => true,
        ]);
        $response->assertRedirect(route('clinical-report-templates.index'));
        $this->assertDatabaseHas('clinical_report_templates', ['name' => 'SOAP Canino Geral']);
    }

    public function test_auto_slug()
    {
        $this->post(route('clinical-report-templates.store'), [
            'name' => 'Modelo Teste', 'content' => 'teste',
        ]);
        $this->assertDatabaseHas('clinical_report_templates', ['slug' => 'modelo-teste']);
    }

    public function test_species_scope()
    {
        ClinicalReportTemplate::factory()->create(['species' => 'canine', 'name' => 'C1']);
        ClinicalReportTemplate::factory()->create(['species' => 'feline', 'name' => 'F1']);
        $results = ClinicalReportTemplate::forSpecies('canine')->get();
        $this->assertTrue($results->contains('name', 'C1'));
        $this->assertFalse($results->contains('name', 'F1'));
    }
}
