<?php

namespace Tests\Feature\Livewire;

use App\Models\CommunicationTemplate;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class CommunicationTemplateFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_template()
    {
        Livewire::test('communication-template-form')
            ->set('name', 'Lembrete de Consulta')
            ->set('type', 'email')
            ->set('channel', 'whatsapp')
            ->set('content', 'Olá, lembrete de consulta amanhã.')
            ->call('save')
            ->assertDispatched('communication-template-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('communication_templates', ['name' => 'Lembrete de Consulta']);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('communication-template-form')
            ->call('save')
            ->assertHasErrors(['name', 'type', 'channel', 'content']);
    }

    public function test_can_edit_existing_template()
    {
        $template = CommunicationTemplate::create([
            'name' => 'Template Antigo',
            'type' => 'email',
            'channel' => 'whatsapp',
            'content' => 'Conteúdo antigo',
        ]);

        Livewire::test('communication-template-form', ['id' => $template->id])
            ->assertSet('name', 'Template Antigo')
            ->set('name', 'Template Novo')
            ->call('save')
            ->assertDispatched('communication-template-saved');

        $this->assertDatabaseHas('communication_templates', [
            'id' => $template->id,
            'name' => 'Template Novo',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $template = CommunicationTemplate::create([
            'name' => 'Event Template',
            'type' => 'sms',
            'channel' => 'sms',
            'content' => 'Event content',
        ]);

        Livewire::test('communication-template-form')
            ->dispatch('editCommunicationTemplate', id: $template->id)
            ->assertSet('communicationTemplateId', $template->id)
            ->assertSet('name', 'Event Template')
            ->call('save')
            ->assertDispatched('communication-template-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $template = CommunicationTemplate::create([
            'name' => 'Temp',
            'type' => 'email',
            'channel' => 'email',
            'content' => 'Temp content',
        ]);

        Livewire::test('communication-template-form')
            ->dispatch('editCommunicationTemplate', id: $template->id)
            ->assertSet('communicationTemplateId', $template->id)
            ->dispatch('resetForm')
            ->assertSet('communicationTemplateId', null)
            ->assertSet('name', '')
            ->assertSet('type', '')
            ->assertSet('channel', '')
            ->assertSet('content', '')
            ->assertSet('is_active', true);
    }

    public function test_can_create_with_subject()
    {
        Livewire::test('communication-template-form')
            ->set('name', 'Template com Assunto')
            ->set('type', 'email')
            ->set('channel', 'email')
            ->set('subject', 'Assunto do Email')
            ->set('content', 'Conteúdo do email')
            ->call('save')
            ->assertDispatched('communication-template-saved');

        $this->assertDatabaseHas('communication_templates', [
            'name' => 'Template com Assunto',
            'subject' => 'Assunto do Email',
        ]);
    }
}
