<?php

namespace Tests\Feature\Livewire;

use App\Models\Category;
use App\Models\Service;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ServiceFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_service()
    {
        Livewire::test('service-form')
            ->set('name', 'Consulta Geral')
            ->set('price', '150.00')
            ->call('save')
            ->assertDispatched('service-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('services', ['name' => 'Consulta Geral', 'price' => 150.00]);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('service-form')
            ->call('save')
            ->assertHasErrors(['name', 'price']);
    }

    public function test_can_edit_existing_service()
    {
        $service = Service::factory()->create([
            'name' => 'Serviço Antigo',
            'price' => 100.00,
        ]);

        Livewire::test('service-form', ['id' => $service->id])
            ->assertSet('name', 'Serviço Antigo')
            ->set('name', 'Serviço Novo')
            ->set('price', '200.00')
            ->call('save')
            ->assertDispatched('service-saved');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Serviço Novo',
            'price' => 200.00,
        ]);
    }

    public function test_can_edit_via_event()
    {
        $service = Service::factory()->create([
            'name' => 'Event Service',
            'price' => 80.00,
        ]);

        Livewire::test('service-form')
            ->dispatch('editService', id: $service->id)
            ->assertSet('serviceId', $service->id)
            ->assertSet('name', 'Event Service')
            ->call('save')
            ->assertDispatched('service-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $service = Service::factory()->create();

        Livewire::test('service-form')
            ->dispatch('editService', id: $service->id)
            ->assertSet('serviceId', $service->id)
            ->dispatch('resetForm')
            ->assertSet('serviceId', null)
            ->assertSet('name', '')
            ->assertSet('price', '')
            ->assertSet('is_active', true);
    }

    public function test_can_create_with_category()
    {
        $category = Category::factory()->create(['type' => 'service']);

        Livewire::test('service-form')
            ->set('name', 'Banho')
            ->set('price', '60.00')
            ->set('category_id', (string) $category->id)
            ->call('save')
            ->assertDispatched('service-saved');

        $this->assertDatabaseHas('services', [
            'name' => 'Banho',
            'category_id' => $category->id,
        ]);
    }

    public function test_can_deactivate_service()
    {
        Livewire::test('service-form')
            ->set('name', 'Serviço Inativo')
            ->set('price', '50.00')
            ->set('is_active', false)
            ->call('save')
            ->assertDispatched('service-saved');

        $this->assertDatabaseHas('services', [
            'name' => 'Serviço Inativo',
            'is_active' => false,
        ]);
    }
}
