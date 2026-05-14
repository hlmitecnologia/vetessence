<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class ModuleTestCase extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (Role::count() === 0) {
            $this->seedRoles();
        }
    }

    protected function seedRoles(): void
    {
        $roles = [
            ['name' => 'Administrador', 'slug' => 'admin'],
            ['name' => 'Veterinário', 'slug' => 'veterinario'],
            ['name' => 'Recepcionista', 'slug' => 'recepcionista'],
            ['name' => 'Financeiro', 'slug' => 'financeiro'],
            ['name' => 'Estoque', 'slug' => 'estoque'],
            ['name' => 'Tutor', 'slug' => 'tutor'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }

    protected function makeUser(string $roleSlug, array $overrides = []): User
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        $user = User::factory()->create(array_merge([
            'role_id' => $role->id,
            'is_active' => true,
        ], $overrides));

        $user->assignRole($role->name);

        return $user;
    }

    protected function loginAs(string $roleSlug): User
    {
        $user = $this->makeUser($roleSlug);
        $this->actingAs($user);
        return $user;
    }
}
