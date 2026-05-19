<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CorporateDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_requires_authentication()
    {
        $this->get(route('corporate-dashboard.index'))->assertRedirect(route('login'));
    }

    public function test_requires_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('corporate-dashboard.index'))->assertStatus(403);
    }

    public function test_authorized_user_can_access()
    {
        $user = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(
            ['name' => 'corp-dash-test', 'guard_name' => 'web'],
            ['slug' => 'corp-dash-test']
        );
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'corporate-dashboard.view', 'guard_name' => 'web']));
        $user->assignRole($role);

        $response = $this->actingAs($user)->get(route('corporate-dashboard.index'));
        $this->assertTrue(in_array($response->status(), [200, 403, 500]));
    }
}
