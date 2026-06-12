<?php

namespace Tests\Feature\Controllers;

use App\Models\ClinicalReportTemplate;
use Tests\ModuleTestCase;

class ClinicalReportTemplateControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        ClinicalReportTemplate::factory()->count(3)->create();
        $response = $this->get(route('clinical-report-templates.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_search()
    {
        ClinicalReportTemplate::factory()->create(['name' => 'Modelo Raio-X']);
        ClinicalReportTemplate::factory()->create(['name' => 'Modelo Ultrassom']);
        $response = $this->get(route('clinical-report-templates.index', ['search' => 'Raio']));
        $response->assertOk();
        $response->assertSee('Modelo Raio-X');
    }

    public function test_index_filters_by_species()
    {
        ClinicalReportTemplate::factory()->create(['name' => 'Canino Template', 'species' => 'canine']);
        ClinicalReportTemplate::factory()->create(['name' => 'Felino Template', 'species' => 'feline']);
        $response = $this->get(route('clinical-report-templates.index', ['species' => 'canine']));
        $response->assertOk();
        $response->assertSee('Canino Template');
    }

    public function test_create()
    {
        $response = $this->get(route('clinical-report-templates.create'));
        $response->assertOk();
    }

    public function test_store_creates_template()
    {
        $response = $this->post(route('clinical-report-templates.store'), [
            'name' => 'Modelo de Raio-X',
            'species' => 'canine',
            'specialty' => 'orthopedics',
            'category' => 'imaging',
            'description' => 'Template para laudos de raio-x',
            'content' => '<p>Paciente: {{patient_name}}</p>',
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('clinical_report_templates', [
            'name' => 'Modelo de Raio-X',
            'slug' => 'modelo-de-raio-x',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('clinical-report-templates.store'), []);
        $response->assertSessionHasErrors(['name', 'content']);
    }

    public function test_show()
    {
        $template = ClinicalReportTemplate::factory()->create();
        $response = $this->get(route('clinical-report-templates.show', $template));
        $response->assertOk();
    }

    public function test_edit()
    {
        $template = ClinicalReportTemplate::factory()->create();
        $response = $this->get(route('clinical-report-templates.edit', $template));
        $response->assertOk();
    }

    public function test_update_modifies_template()
    {
        $template = ClinicalReportTemplate::factory()->create(['name' => 'Old Name']);
        $response = $this->put(route('clinical-report-templates.update', $template), [
            'name' => 'Updated Template',
            'content' => '<p>Updated content</p>',
        ]);
        $response->assertRedirect();
        $this->assertDatabaseHas('clinical_report_templates', [
            'id' => $template->id,
            'name' => 'Updated Template',
        ]);
    }

    public function test_destroy_deletes_template()
    {
        $template = ClinicalReportTemplate::factory()->create();
        $response = $this->delete(route('clinical-report-templates.destroy', $template));
        $response->assertRedirect();
        $this->assertDatabaseMissing('clinical_report_templates', ['id' => $template->id]);
    }
}
