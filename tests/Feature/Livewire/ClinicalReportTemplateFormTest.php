<?php

namespace Tests\Feature\Livewire;

use App\Models\ClinicalReportTemplate;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ClinicalReportTemplateFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_template()
    {
        Livewire::test('clinical-report-template-form')
            ->set('name', 'Relatório de Castração')
            ->set('content', '<p>Conteúdo do relatório</p>')
            ->call('save')
            ->assertDispatched('clinical-report-template-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('clinical_report_templates', ['name' => 'Relatório de Castração']);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('clinical-report-template-form')
            ->call('save')
            ->assertHasErrors(['name', 'content']);
    }

    public function test_can_edit_existing_template()
    {
        $template = ClinicalReportTemplate::factory()->create([
            'name' => 'Template Antigo',
        ]);

        Livewire::test('clinical-report-template-form', ['id' => $template->id])
            ->assertSet('name', $template->name)
            ->set('name', 'Template Novo')
            ->call('save')
            ->assertDispatched('clinical-report-template-saved');

        $this->assertDatabaseHas('clinical_report_templates', [
            'id' => $template->id,
            'name' => 'Template Novo',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $template = ClinicalReportTemplate::factory()->create([
            'name' => 'Event Template',
        ]);

        Livewire::test('clinical-report-template-form')
            ->dispatch('editClinicalReportTemplate', id: $template->id)
            ->assertSet('clinicalReportTemplateId', $template->id)
            ->assertSet('name', 'Event Template')
            ->call('save')
            ->assertDispatched('clinical-report-template-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $template = ClinicalReportTemplate::factory()->create();

        Livewire::test('clinical-report-template-form')
            ->dispatch('editClinicalReportTemplate', id: $template->id)
            ->assertSet('clinicalReportTemplateId', $template->id)
            ->dispatch('resetForm')
            ->assertSet('clinicalReportTemplateId', null)
            ->assertSet('name', '')
            ->assertSet('content', '')
            ->assertSet('is_active', true);
    }

    public function test_can_deactivate_template()
    {
        Livewire::test('clinical-report-template-form')
            ->set('name', 'Template Inativo')
            ->set('content', '<p>Teste</p>')
            ->set('is_active', false)
            ->call('save')
            ->assertDispatched('clinical-report-template-saved');

        $this->assertDatabaseHas('clinical_report_templates', [
            'name' => 'Template Inativo',
            'is_active' => false,
        ]);
    }
}
