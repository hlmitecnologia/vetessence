<?php

namespace Tests\Unit\Commands;

use App\Models\Role as AppRole;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role as SpatieRole;
use Spatie\Permission\PermissionRegistrar;
use Tests\ModuleTestCase;

class SyncSpatieRolesTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Cache::tags(config('permission.cache.tag'))->flush();
    }

    public function test_syncs_roles_and_permissions(): void
    {
        $roleName = 'SyncVet_' . uniqid();
        $appRole = AppRole::create(['name' => $roleName, 'slug' => 'sync-vet-' . uniqid()]);
        $spatieRole = SpatieRole::findOrCreate($roleName, 'web');

        $user = User::factory()->create([
            'role_id' => $appRole->id,
        ]);

        $exitCode = Artisan::call('roles:sync-spatie');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString("Atribuída '{$spatieRole->name}' para {$user->name}", $output);

        $this->assertDatabaseHas('model_has_roles', [
            'role_id' => $spatieRole->id,
            'model_id' => $user->id,
        ]);
    }

    public function test_handles_command_with_no_users(): void
    {
        User::whereNotNull('role_id')->update(['role_id' => null]);

        $exitCode = Artisan::call('roles:sync-spatie');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode);
        $this->assertStringContainsString('0 usuários sincronizados', $output);
    }

    public function test_handles_missing_spatie_role(): void
    {
        $roleName = 'MissingRole_' . uniqid();
        $appRole = AppRole::create(['name' => $roleName, 'slug' => 'missing-role-' . uniqid()]);

        User::factory()->create([
            'role_id' => $appRole->id,
        ]);

        $exitCode = Artisan::call('roles:sync-spatie');
        $this->assertEquals(0, $exitCode);
    }
}
