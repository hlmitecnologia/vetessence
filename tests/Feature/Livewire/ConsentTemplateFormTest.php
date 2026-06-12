<?php

namespace Tests\Feature\Livewire;

use App\Models\ConsentTemplate;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ConsentTemplateFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_template()
    {
        Livewire::test('consent-template-form')
            ->set('name', 'Termo de Cirurgia')
            ->set('content', '<p>Conteúdo do termo</p>')
            ->set('category', 'cirurgia')
            ->call('save')
            ->assertDispatched('consent-template-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('consent_templates', ['name' => 'Termo de Cirurgia']);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('consent-template-form')
            ->call('save')
            ->assertHasErrors(['name', 'content']);
    }

    public function test_can_edit_existing_template()
    {
        $template = ConsentTemplate::create([
            'name' => 'Termo Antigo',
            'content' => '<p>Conteúdo antigo</p>',
        ]);

        Livewire::test('consent-template-form', ['id' => $template->id])
            ->assertSet('name', 'Termo Antigo')
            ->set('name', 'Termo Novo')
            ->call('save')
            ->assertDispatched('consent-template-saved');

        $this->assertDatabaseHas('consent_templates', [
            'id' => $template->id,
            'name' => 'Termo Novo',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $template = ConsentTemplate::create([
            'name' => 'Event Termo',
            'content' => '<p>Event content</p>',
        ]);

        Livewire::test('consent-template-form')
            ->dispatch('editConsentTemplate', id: $template->id)
            ->assertSet('consentTemplateId', $template->id)
            ->assertSet('name', 'Event Termo')
            ->call('save')
            ->assertDispatched('consent-template-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $template = ConsentTemplate::create([
            'name' => 'Temp',
            'content' => '<p>Temp</p>',
        ]);

        Livewire::test('consent-template-form')
            ->dispatch('editConsentTemplate', id: $template->id)
            ->assertSet('consentTemplateId', $template->id)
            ->dispatch('resetForm')
            ->assertSet('consentTemplateId', null)
            ->assertSet('name', '')
            ->assertSet('content', '')
            ->assertSet('is_active', true);
    }

    public function test_can_deactivate_template()
    {
        Livewire::test('consent-template-form')
            ->set('name', 'Termo Inativo')
            ->set('content', '<p>Teste</p>')
            ->set('category', 'cirurgia')
            ->set('is_active', false)
            ->call('save')
            ->assertDispatched('consent-template-saved');

        $this->assertDatabaseHas('consent_templates', [
            'name' => 'Termo Inativo',
            'is_active' => false,
        ]);
    }

    public function test_can_create_with_category()
    {
        Livewire::test('consent-template-form')
            ->set('name', 'Termo Categorizado')
            ->set('content', '<p>Conteúdo</p>')
            ->set('category', 'cirurgia')
            ->call('save')
            ->assertDispatched('consent-template-saved');

        $this->assertDatabaseHas('consent_templates', [
            'name' => 'Termo Categorizado',
            'category' => 'cirurgia',
        ]);
    }
}
