<?php

namespace Tests\Feature\Livewire;

use App\Models\GroomingTemplate;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class GroomingTemplateFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create()
    {
        Livewire::test('grooming-template-form')
            ->set('name', 'Banho e Tosa')
            ->set('price', 75.50)
            ->set('estimated_minutes', 60)
            ->set('services', '["wash", "dry"]')
            ->call('save')
            ->assertDispatched('grooming-template-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('grooming_templates', [
            'name' => 'Banho e Tosa',
            'price' => 75.50,
            'estimated_minutes' => 60,
        ]);

        $template = GroomingTemplate::where('name', 'Banho e Tosa')->first();
        $this->assertEquals(['wash', 'dry'], $template->services);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('grooming-template-form')
            ->call('save')
            ->assertHasErrors(['name', 'price', 'estimated_minutes']);
    }

    public function test_can_edit()
    {
        $template = GroomingTemplate::factory()->create([
            'name' => 'Banho Simples',
            'price' => 50.00,
            'estimated_minutes' => 30,
        ]);

        Livewire::test('grooming-template-form', ['id' => $template->id])
            ->assertSet('name', $template->name)
            ->set('name', 'Banho Completo')
            ->set('price', 80.00)
            ->call('save')
            ->assertDispatched('grooming-template-saved');

        $this->assertDatabaseHas('grooming_templates', [
            'id' => $template->id,
            'name' => 'Banho Completo',
            'price' => 80.00,
        ]);
    }

    public function test_can_edit_via_event()
    {
        $template = GroomingTemplate::factory()->create([
            'name' => 'Tosa Higiênica',
        ]);

        Livewire::test('grooming-template-form')
            ->dispatch('editGroomingTemplate', id: $template->id)
            ->assertSet('name', 'Tosa Higiênica')
            ->set('name', 'Tosa Completa')
            ->call('save')
            ->assertDispatched('grooming-template-saved');

        $this->assertDatabaseHas('grooming_templates', [
            'id' => $template->id,
            'name' => 'Tosa Completa',
        ]);
    }

    public function test_reset_form()
    {
        $template = GroomingTemplate::factory()->create();

        Livewire::test('grooming-template-form')
            ->dispatch('editGroomingTemplate', id: $template->id)
            ->assertSet('groomingTemplateId', $template->id)
            ->dispatch('resetForm')
            ->assertSet('groomingTemplateId', null)
            ->assertSet('name', '')
            ->assertSet('price', '')
            ->assertSet('estimated_minutes', '')
            ->assertSet('is_active', true);
    }
}
