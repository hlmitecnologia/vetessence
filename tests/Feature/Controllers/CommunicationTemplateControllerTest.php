<?php

namespace Tests\Feature\Controllers;

use App\Models\CommunicationTemplate;
use Tests\ModuleTestCase;

class CommunicationTemplateControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    private function createTemplate(array $overrides = []): CommunicationTemplate
    {
        return CommunicationTemplate::create(array_merge([
            'name' => 'Lembrete de Consulta',
            'type' => 'appointment_reminder',
            'channel' => 'email',
            'subject' => 'Sua consulta está agendada',
            'content' => 'Olá, sua consulta será em breve.',
            'is_active' => true,
        ], $overrides));
    }

    public function test_index()
    {
        $response = $this->get(route('communication-templates.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('communication-templates.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('communication-templates.store'), [
            'name' => 'Lembrete de Consulta',
            'type' => 'appointment_reminder',
            'channel' => 'email',
            'subject' => 'Sua consulta está agendada',
            'content' => 'Olá, sua consulta será em breve.',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('communication-templates.index'));
        $this->assertDatabaseHas('communication_templates', ['name' => 'Lembrete de Consulta']);
    }

    public function test_show()
    {
        $template = $this->createTemplate();

        $response = $this->get(route('communication-templates.show', $template));
        $response->assertOk();
    }

    public function test_edit()
    {
        $template = $this->createTemplate();

        $response = $this->get(route('communication-templates.edit', $template));
        $response->assertOk();
    }

    public function test_update()
    {
        $template = $this->createTemplate();

        $response = $this->put(route('communication-templates.update', $template), [
            'name' => 'Template Atualizado',
            'type' => 'vaccination_reminder',
            'channel' => 'whatsapp',
            'subject' => 'Vacinação',
            'content' => 'Conteúdo atualizado.',
            'is_active' => false,
        ]);

        $response->assertRedirect(route('communication-templates.index'));
        $this->assertDatabaseHas('communication_templates', ['name' => 'Template Atualizado']);
    }

    public function test_destroy()
    {
        $template = $this->createTemplate();

        $response = $this->delete(route('communication-templates.destroy', $template));
        $response->assertRedirect(route('communication-templates.index'));
        $this->assertDatabaseMissing('communication_templates', ['id' => $template->id]);
    }
}
