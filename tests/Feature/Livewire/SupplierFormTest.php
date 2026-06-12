<?php

namespace Tests\Feature\Livewire;

use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\Supplier;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class SupplierFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_supplier()
    {
        Livewire::test('supplier-form')
            ->set('name', 'Fornecedor Ltda')
            ->call('save')
            ->assertDispatched('supplier-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('suppliers', ['name' => 'Fornecedor Ltda']);
    }

    public function test_validates_required_fields()
    {
        Livewire::test('supplier-form')
            ->call('save')
            ->assertHasErrors(['name']);
    }

    public function test_can_edit_existing_supplier()
    {
        $supplier = Supplier::factory()->create([
            'name' => 'Fornecedor Antigo',
        ]);

        Livewire::test('supplier-form', ['id' => $supplier->id])
            ->assertSet('name', 'Fornecedor Antigo')
            ->set('name', 'Fornecedor Novo')
            ->call('save')
            ->assertDispatched('supplier-saved');

        $this->assertDatabaseHas('suppliers', [
            'id' => $supplier->id,
            'name' => 'Fornecedor Novo',
        ]);
    }

    public function test_can_edit_via_event()
    {
        $supplier = Supplier::factory()->create([
            'name' => 'Event Supplier',
        ]);

        Livewire::test('supplier-form')
            ->dispatch('editSupplier', id: $supplier->id)
            ->assertSet('supplierId', $supplier->id)
            ->assertSet('name', 'Event Supplier')
            ->call('save')
            ->assertDispatched('supplier-saved');
    }

    public function test_reset_form_clears_properties()
    {
        $supplier = Supplier::factory()->create();

        Livewire::test('supplier-form')
            ->dispatch('editSupplier', id: $supplier->id)
            ->assertSet('supplierId', $supplier->id)
            ->dispatch('resetForm')
            ->assertSet('supplierId', null)
            ->assertSet('name', '')
            ->assertSet('cnpj', '')
            ->assertSet('phone', '')
            ->assertSet('email', '');
    }

    public function test_can_create_with_branch()
    {
        $branch = Branch::factory()->create();

        Livewire::test('supplier-form')
            ->set('name', 'Fornecedor Filial')
            ->set('branch_id', (string) $branch->id)
            ->call('save')
            ->assertDispatched('supplier-saved');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Fornecedor Filial',
            'branch_id' => $branch->id,
        ]);
    }

    public function test_can_create_with_state_and_city()
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);

        Livewire::test('supplier-form')
            ->set('name', 'Fornecedor Localizado')
            ->set('state_id', (string) $state->id)
            ->call('save')
            ->assertDispatched('supplier-saved');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Fornecedor Localizado',
            'state_id' => $state->id,
        ]);
    }

    public function test_can_create_with_all_contact_info()
    {
        Livewire::test('supplier-form')
            ->set('name', 'Fornecedor Completo')
            ->set('cnpj', '11.222.333/0001-44')
            ->set('phone', '11999999999')
            ->set('email', 'fornecedor@teste.com')
            ->set('address', 'Rua Teste, 123')
            ->set('contact', 'João')
            ->call('save')
            ->assertDispatched('supplier-saved');

        $this->assertDatabaseHas('suppliers', [
            'name' => 'Fornecedor Completo',
            'email' => 'fornecedor@teste.com',
            'contact' => 'João',
        ]);
    }
}
