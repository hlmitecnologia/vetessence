<?php

namespace Tests\Feature\Livewire;

use App\Models\Convenio;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class ConvenioFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_convenio()
    {
        Livewire::test('convenio-form')
            ->set('name', 'Plano Pet Saúde')
            ->call('save')
            ->assertDispatched('convenio-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('convenios', ['name' => 'Plano Pet Saúde']);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('convenio-form')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_can_edit_existing_convenio()
    {
        $convenio = Convenio::factory()->create([
            'name' => 'Plano Antigo',
        ]);

        Livewire::test('convenio-form', ['id' => $convenio->id])
            ->assertSet('name', 'Plano Antigo')
            ->set('name', 'Plano Novo')
            ->call('save')
            ->assertDispatched('convenio-saved');

        $this->assertDatabaseHas('convenios', [
            'id' => $convenio->id,
            'name' => 'Plano Novo',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $convenio = Convenio::factory()->create([
            'name' => 'Event Plano',
        ]);

        Livewire::test('convenio-form')
            ->dispatch('editConvenio', id: $convenio->id)
            ->assertSet('convenioId', $convenio->id)
            ->assertSet('name', 'Event Plano')
            ->call('save')
            ->assertDispatched('convenio-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $convenio = Convenio::factory()->create();

        Livewire::test('convenio-form')
            ->dispatch('editConvenio', id: $convenio->id)
            ->assertSet('convenioId', $convenio->id)
            ->dispatch('resetForm')
            ->assertSet('convenioId', null)
            ->assertSet('name', '')
            ->assertSet('cnpj', '')
            ->assertSet('is_active', true);
    }

    public function test_can_create_with_optional_fields()
    {
        Livewire::test('convenio-form')
            ->set('name', 'Plano Completo')
            ->set('cnpj', '12.345.678/0001-90')
            ->set('discount_percent', '10')
            ->set('max_consults_month', '4')
            ->call('save')
            ->assertDispatched('convenio-saved');

        $this->assertDatabaseHas('convenios', [
            'name' => 'Plano Completo',
            'discount_percent' => 10.00,
            'max_consults_month' => 4,
        ]);
    }

    public function test_can_deactivate_convenio()
    {
        Livewire::test('convenio-form')
            ->set('name', 'Plano Inativo')
            ->set('is_active', false)
            ->call('save')
            ->assertDispatched('convenio-saved');

        $this->assertDatabaseHas('convenios', [
            'name' => 'Plano Inativo',
            'is_active' => false,
        ]);
    }
}
