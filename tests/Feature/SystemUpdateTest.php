<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SystemUpdateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_requires_authentication()
    {
        $this->get(route('system-update.index'))->assertRedirect(route('login'));
    }

    public function test_requires_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('system-update.index'))->assertStatus(403);
    }

    public function test_authorized_user_can_access()
    {
        $user = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'su-test', 'guard_name' => 'web'],
            ['slug' => 'su-test']
        );
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'system-update', 'guard_name' => 'web']));
        $user->assignRole($role);

        $this->actingAs($user)->get(route('system-update.index'))->assertOk();
    }

    public function test_token_can_be_saved()
    {
        $user = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'su-token-test', 'guard_name' => 'web'],
            ['slug' => 'su-token-test']
        );
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'system-update', 'guard_name' => 'web']));
        $user->assignRole($role);

        $this->actingAs($user)->post(route('system-update.token'), [
            'github_token' => 'ghp_test123',
            'github_repo' => 'hlmitecnologia/vetessence',
            'github_branch' => 'main',
        ])->assertRedirect(route('system-update.index'));

        $this->assertNotNull(Setting::get('github_token'));
        $this->assertEquals('ghp_test123', Setting::getEncrypted('github_token'));
    }
}
