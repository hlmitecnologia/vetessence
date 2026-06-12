<?php

namespace Tests\Feature\Controllers;

use App\Models\GroomingTemplate;
use Tests\ModuleTestCase;

class GroomingTemplateControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        GroomingTemplate::factory()->count(3)->create();
        $response = $this->get(route('grooming-templates.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('grooming-templates.create'));
        $response->assertOk();
    }

    public function test_store_creates_template()
    {
        $response = $this->post(route('grooming-templates.store'), [
            'name' => 'Banho Premium',
            'species' => 'canino',
            'price' => 89.90,
            'estimated_minutes' => 60,
            'is_active' => true,
        ]);

        $response->assertRedirect(route('grooming-templates.index'));
        $this->assertDatabaseHas('grooming_templates', [
            'name' => 'Banho Premium',
            'price' => 89.90,
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('grooming-templates.store'), []);
        $response->assertSessionHasErrors(['name', 'price', 'estimated_minutes']);
    }

    public function test_show()
    {
        $template = GroomingTemplate::factory()->create();
        $response = $this->get(route('grooming-templates.show', $template));
        $response->assertOk();
    }

    public function test_edit()
    {
        $template = GroomingTemplate::factory()->create();
        $response = $this->get(route('grooming-templates.edit', $template));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $template = GroomingTemplate::factory()->create(['price' => 50.00]);

        $response = $this->put(route('grooming-templates.update', $template), [
            'name' => $template->name,
            'price' => 99.90,
            'estimated_minutes' => 45,
        ]);

        $response->assertRedirect(route('grooming-templates.index'));
        $this->assertDatabaseHas('grooming_templates', [
            'id' => $template->id,
            'price' => 99.90,
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $template = GroomingTemplate::factory()->create();

        $response = $this->delete(route('grooming-templates.destroy', $template));

        $response->assertRedirect(route('grooming-templates.index'));
        $this->assertDatabaseMissing('grooming_templates', ['id' => $template->id]);
    }
}
