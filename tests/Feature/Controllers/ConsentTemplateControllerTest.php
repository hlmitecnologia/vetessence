<?php

namespace Tests\Feature\Controllers;

use App\Models\ConsentForm;
use App\Models\ConsentTemplate;
use Tests\ModuleTestCase;

class ConsentTemplateControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        ConsentTemplate::create([
            'name' => 'Termo de Cirurgia',
            'content' => '<p>Conteúdo</p>',
            'slug' => 'termo-cirurgia',
            'is_active' => true,
        ]);

        $response = $this->get(route('consent-templates.index'));

        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('consent-templates.store'), [
            'name' => 'Termo de Cirurgia',
            'content' => '<p>Eu autorizo...</p>',
            'category' => 'cirurgia',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('consent-templates.index'));
        $this->assertDatabaseHas('consent_templates', ['name' => 'Termo de Cirurgia']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('consent-templates.store'), []);

        $response->assertSessionHasErrors(['name', 'content']);
    }

    public function test_show()
    {
        $template = ConsentTemplate::create([
            'name' => 'Termo de Cirurgia',
            'content' => '<p>Conteúdo</p>',
            'slug' => 'termo-cirurgia',
            'is_active' => true,
        ]);

        $response = $this->get(route('consent-templates.show', $template));

        $response->assertOk();
    }

    public function test_update()
    {
        $template = ConsentTemplate::create([
            'name' => 'Termo de Cirurgia',
            'content' => '<p>Conteúdo</p>',
            'slug' => 'termo-cirurgia',
            'is_active' => true,
        ]);

        $response = $this->put(route('consent-templates.update', $template), [
            'name' => 'Termo Atualizado',
            'content' => '<p>Conteúdo atualizado</p>',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('consent-templates.index'));
        $this->assertDatabaseHas('consent_templates', [
            'id' => $template->id,
            'name' => 'Termo Atualizado',
        ]);
    }

    public function test_destroy()
    {
        $template = ConsentTemplate::create([
            'name' => 'Termo de Cirurgia',
            'content' => '<p>Conteúdo</p>',
            'slug' => 'termo-cirurgia',
            'is_active' => true,
        ]);

        $response = $this->delete(route('consent-templates.destroy', $template));

        $response->assertRedirect(route('consent-templates.index'));
        $this->assertDatabaseMissing('consent_templates', ['id' => $template->id]);
    }

    public function test_destroy_fails_when_linked_to_consent_forms()
    {
        $template = ConsentTemplate::create([
            'name' => 'Termo de Cirurgia',
            'content' => '<p>Conteúdo</p>',
            'slug' => 'termo-cirurgia',
            'is_active' => true,
        ]);
        ConsentForm::factory()->create(['consent_template_id' => $template->id]);

        $response = $this->delete(route('consent-templates.destroy', $template));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('consent_templates', ['id' => $template->id]);
    }
}
