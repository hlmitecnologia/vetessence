<?php

namespace Tests\Browser;

use App\Models\Branch;
use App\Models\Role as AppRole;
use App\Models\User;
use Database\Seeders\PermissionSeeder;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;
use Spatie\Permission\Models\Role as SpatieRole;

trait TestsFlows
{
    protected static bool $migrated = false;

    protected function setUpDuskFlows(): void
    {
        if (!static::$migrated) {
            $this->artisan('migrate:fresh');
            $this->artisan('db:seed', ['--class' => RoleSeeder::class]);
            $this->artisan('db:seed', ['--class' => PermissionSeeder::class]);
            static::$migrated = true;
        }
    }

    protected function createUser(string $roleSlug, array $overrides = []): User
    {
        $spatieRole = SpatieRole::where('name', $roleSlug)->first();

        if (!$spatieRole) {
            $appRole = AppRole::where('slug', $roleSlug)->firstOrFail();
            $spatieRole = SpatieRole::findById($appRole->id);
        }

        $appRole = AppRole::where('slug', $roleSlug)->first();

        $user = User::factory()->create(array_merge([
            'password' => Hash::make('password'),
            'role_id' => $appRole?->id,
        ], $overrides));

        $user->assignRole($spatieRole);

        return $user;
    }

    protected function createBranch(): Branch
    {
        return Branch::factory()->create([
            'name' => 'Unidade Teste',
            'is_active' => true,
        ]);
    }

    protected function loginAs(Browser $browser, User $user): Browser
    {
        $browser->loginAs($user);
        return $browser;
    }

    protected function assertSuccess(Browser $browser): void
    {
        $browser->assertMissing('.alert-danger');
    }

    protected function assertForbidden(Browser $browser): void
    {
        $browser->assertSee('403');
    }
}
