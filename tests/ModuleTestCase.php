<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Role as SpatieRole;

abstract class ModuleTestCase extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        if (SpatieRole::count() === 0) {
            SpatieRole::create(['name' => 'super-admin', 'guard_name' => 'web']);
        }
    }

    protected function makeUser(string $roleSlug, array $overrides = []): User
    {
        $role = Role::where('slug', $roleSlug)->first();

        if (!$role) {
            $role = Role::create(['name' => ucfirst($roleSlug), 'slug' => $roleSlug]);
        }

        $user = User::factory()->create(array_merge([
            'role_id' => $role->id,
            'is_active' => true,
        ], $overrides));

        $user->assignRole('super-admin');

        return $user;
    }

    protected function loginAs(string $roleSlug): User
    {
        $user = $this->makeUser($roleSlug);
        $this->actingAs($user);
        return $user;
    }
}
