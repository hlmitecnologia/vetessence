<?php

namespace Tests\Feature\Modules;

use App\Models\GroomingTemplate;
use Tests\ModuleTestCase;

class GroomingTemplateTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('grooming-templates.index'));
        $response->assertOk();
    }

    public function test_can_create()
    {
        $response = $this->post(route('grooming-templates.store'), [
            'name' => 'Banho Simples',
            'price' => 50.00,
            'estimated_minutes' => 45,
        ]);
        $response->assertRedirect(route('grooming-templates.index'));
        $this->assertDatabaseHas('grooming_templates', ['name' => 'Banho Simples']);
    }

    public function test_active_scope()
    {
        GroomingTemplate::factory()->create(['is_active' => true]);
        GroomingTemplate::factory()->create(['is_active' => false]);
        $this->assertCount(1, GroomingTemplate::where('is_active', true)->get());
    }
}
