<?php

namespace Tests\Feature\Controllers;

use App\Models\Role;
use Tests\ModuleTestCase;

class RoleControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('roles.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('roles.create'));
        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('roles.store'), [
            'name' => 'Gerente',
            'slug' => 'gerente',
            'description' => 'Gerente da clínica',
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['slug' => 'gerente']);
    }

    public function test_show()
    {
        $role = Role::first();

        $response = $this->get(route('roles.show', $role));
        $response->assertOk();
    }

    public function test_edit()
    {
        $role = Role::first();

        $response = $this->get(route('roles.edit', $role));
        $response->assertOk();
    }

    public function test_update()
    {
        $role = Role::first();

        $response = $this->put(route('roles.update', $role), [
            'name' => 'Admin Atualizado',
            'slug' => 'admin-atualizado',
            'description' => 'Descrição atualizada',
        ]);

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseHas('roles', ['name' => 'Admin Atualizado']);
    }

    public function test_destroy()
    {
        $role = Role::create([
            'name' => 'Teste Role',
            'slug' => 'teste-role',
            'guard_name' => 'web',
            'description' => 'Role para teste',
        ]);

        $response = $this->delete(route('roles.destroy', $role));
        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
