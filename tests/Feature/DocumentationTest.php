<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DocumentationTest extends TestCase
{
    use DatabaseTransactions;

    public function test_requires_authentication()
    {
        $this->get(route('docs.index'))->assertRedirect(route('login'));
    }

    public function test_requires_docs_view_permission()
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('docs.index'))->assertStatus(403);
    }

    public function test_authorized_user_can_access()
    {
        $user = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'docs-test', 'guard_name' => 'web']);
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'docs.view', 'guard_name' => 'web']));
        $user->assignRole($role);

        $this->actingAs($user)->get(route('docs.index'))->assertOk();
    }

    public function test_section_page_renders()
    {
        $user = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'docs-test2', 'guard_name' => 'web']);
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'docs.view', 'guard_name' => 'web']));
        $user->assignRole($role);

        $this->actingAs($user)->get(route('docs.show', ['section' => 'user-manual']))->assertOk();
        $this->actingAs($user)->get(route('docs.show', ['section' => 'technical-manual']))->assertOk();
        $this->actingAs($user)->get(route('docs.show', ['section' => 'changelog']))->assertOk();
    }
}
