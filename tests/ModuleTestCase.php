<?php

namespace Tests;

use App\Http\Middleware\VerifyCsrfToken;
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

        $this->withoutMiddleware(VerifyCsrfToken::class);

        if (!SpatieRole::where('name', 'super-admin')->where('guard_name', 'web')->exists()) {
            SpatieRole::create([
                'name' => 'super-admin',
                'guard_name' => 'web',
                'slug' => 'super-admin-role',
            ]);
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

    protected function loginAs(string $roleSlug, array $overrides = []): User
    {
        $user = $this->makeUser($roleSlug, $overrides);
        $this->actingAs($user);
        return $user;
    }
}
