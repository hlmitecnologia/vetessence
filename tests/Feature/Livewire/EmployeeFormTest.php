<?php

namespace Tests\Feature\Livewire;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class EmployeeFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create_user_with_required_fields()
    {
        Livewire::test('employee-form')
            ->set('name', 'Novo Usuário')
            ->set('email', 'novo@teste.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('save')
            ->assertDispatched('user-saved')
            ->assertDispatched('close-modal');

        $this->assertDatabaseHas('users', [
            'email' => 'novo@teste.com',
            'name' => 'Novo Usuário',
        ]);
    }

    public function test_validates_required_fields_on_create()
    {
        Livewire::test('employee-form')
            ->call('save')
            ->assertHasErrors(['name', 'email', 'password']);
    }

    public function test_validates_password_confirmation()
    {
        Livewire::test('employee-form')
            ->set('name', 'Usuário Teste')
            ->set('email', 'user@teste.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'different')
            ->call('save')
            ->assertHasErrors(['password']);
    }

    public function test_validates_unique_email()
    {
        User::factory()->create(['email' => 'existente@teste.com']);

        Livewire::test('employee-form')
            ->set('name', 'Duplicado')
            ->set('email', 'existente@teste.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('save')
            ->assertHasErrors(['email']);
    }

    public function test_can_edit_existing_user()
    {
        $user = User::factory()->create([
            'name' => 'Nome Antigo',
            'email' => 'editavel@teste.com',
        ]);

        Livewire::test('employee-form', ['id' => $user->id])
            ->assertSet('name', 'Nome Antigo')
            ->set('name', 'Nome Novo')
            ->call('save')
            ->assertDispatched('user-saved');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nome Novo',
        ]);
    }

    public function test_can_edit_user_via_event()
    {
        $user = User::factory()->create([
            'name' => 'Event User',
            'email' => 'event@teste.com',
        ]);

        Livewire::test('employee-form')
            ->dispatch('editUser', id: $user->id)
            ->assertSet('userId', $user->id)
            ->assertSet('name', 'Event User')
            ->assertSet('email', 'event@teste.com')
            ->set('phone', '11988888888')
            ->call('save')
            ->assertDispatched('user-saved');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'phone' => '11988888888']);
    }

    public function test_password_not_required_on_edit()
    {
        $user = User::factory()->create([
            'name' => 'No Password Change',
            'email' => 'nopass@teste.com',
        ]);

        Livewire::test('employee-form', ['id' => $user->id])
            ->set('name', 'Updated Name')
            ->call('save')
            ->assertDispatched('user-saved');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Updated Name']);
    }

    public function test_can_update_password_on_edit()
    {
        $user = User::factory()->create([
            'email' => 'passchange@teste.com',
        ]);

        Livewire::test('employee-form', ['id' => $user->id])
            ->set('password', 'newpassword')
            ->set('password_confirmation', 'newpassword')
            ->call('save')
            ->assertDispatched('user-saved');
    }

    public function test_can_assign_role()
    {
        $role = Role::create(['name' => 'TestRole', 'slug' => 'test-role']);

        Livewire::test('employee-form')
            ->set('name', 'Com Role')
            ->set('email', 'comrole@teste.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('role_id', (string) $role->id)
            ->call('save')
            ->assertDispatched('user-saved');

        $user = User::where('email', 'comrole@teste.com')->first();
        $this->assertEquals($role->id, $user->role_id);
    }

    public function test_can_create_veterinarian_user()
    {
        Livewire::test('employee-form')
            ->set('name', 'Dr. Veterinário')
            ->set('email', 'vet@clinica.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('is_veterinarian', true)
            ->call('save')
            ->assertDispatched('user-saved');

        $this->assertDatabaseHas('users', [
            'email' => 'vet@clinica.com',
            'is_veterinarian' => 1,
        ]);
    }

    public function test_can_edit_is_veterinarian_flag()
    {
        $user = User::factory()->create([
            'name' => 'Dr. Sem CRM',
            'email' => 'semcrm@teste.com',
            'is_veterinarian' => false,
        ]);

        Livewire::test('employee-form', ['id' => $user->id])
            ->assertSet('is_veterinarian', false)
            ->set('is_veterinarian', true)
            ->call('save')
            ->assertDispatched('user-saved');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_veterinarian' => 1,
        ]);
    }

    public function test_can_assign_branch()
    {
        $branch = Branch::factory()->create();

        Livewire::test('employee-form')
            ->set('name', 'Com Filial')
            ->set('email', 'comfilial@teste.com')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('branch_id', (string) $branch->id)
            ->call('save')
            ->assertDispatched('user-saved');

        $user = User::where('email', 'comfilial@teste.com')->first();
        $this->assertEquals($branch->id, $user->branch_id);
    }

    public function test_validates_password_min_length()
    {
        Livewire::test('employee-form')
            ->set('name', 'Short Pass')
            ->set('email', 'short@teste.com')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('save')
            ->assertHasErrors(['password']);
    }

    public function test_reset_form_clears_properties()
    {
        $user = User::factory()->create(['email' => 'reset@teste.com']);

        Livewire::test('employee-form')
            ->dispatch('editUser', id: $user->id)
            ->assertSet('userId', $user->id)
            ->dispatch('resetForm')
            ->assertSet('userId', null)
            ->assertSet('name', '')
            ->assertSet('email', '')
            ->assertSet('password', '')
            ->assertSet('is_active', true)
            ->assertSet('is_veterinarian', false);
    }
}
