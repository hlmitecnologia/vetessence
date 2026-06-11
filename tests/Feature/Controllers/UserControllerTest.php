<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Tests\ModuleTestCase;

class UserControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        User::factory()->count(3)->create();
        $response = $this->get(route('users.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_search()
    {
        User::factory()->create(['name' => 'João Silva']);
        User::factory()->create(['name' => 'Maria Souza']);
        $response = $this->get(route('users.index', ['search' => 'João']));
        $response->assertOk();
    }

    public function test_create_redirects_to_index()
    {
        $response = $this->get(route('users.create'));
        $response->assertRedirect(route('users.index'));
    }

    public function test_store_creates_user()
    {
        Branch::factory()->create();

        $response = $this->post(route('users.store'), [
            'name' => 'Novo Usuário',
            'email' => 'novo@teste.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['email' => 'novo@teste.com']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('users.store'), []);
        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_store_validates_unique_email()
    {
        User::factory()->create(['email' => 'existente@teste.com']);
        $response = $this->post(route('users.store'), [
            'name' => 'Teste',
            'email' => 'existente@teste.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_show()
    {
        $user = User::factory()->create();
        $response = $this->get(route('users.show', $user));
        $response->assertOk();
    }

    public function test_edit_redirects_to_index()
    {
        $user = User::factory()->create();
        $response = $this->get(route('users.edit', $user));
        $response->assertRedirect(route('users.index'));
    }

    public function test_update_modifies_user()
    {
        $user = User::factory()->create(['name' => 'Nome Antigo']);
        $response = $this->put(route('users.update', $user), [
            'name' => 'Nome Novo',
            'email' => $user->email,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nome Novo']);
    }

    public function test_update_validates_email_unique_except_self()
    {
        $user = User::factory()->create(['email' => 'user@teste.com']);
        $other = User::factory()->create(['email' => 'other@teste.com']);

        $response = $this->put(route('users.update', $user), [
            'name' => 'Teste',
            'email' => 'other@teste.com',
        ]);
        $response->assertSessionHasErrors('email');
    }

    public function test_destroy_deletes_user()
    {
        $user = User::factory()->create();
        $response = $this->delete(route('users.destroy', $user));
        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_destroy_blocks_self_deletion()
    {
        $user = $this->loginAs('admin');
        $response = $this->delete(route('users.destroy', $user));
        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }
}
