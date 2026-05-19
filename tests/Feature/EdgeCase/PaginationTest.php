<?php

namespace Tests\Feature\EdgeCase;

use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use DatabaseTransactions;

    private function authorizedUser(): User
    {
        $user = User::factory()->create();
        $role = Role::firstOrCreate(
            ['name' => 'pagination-test', 'guard_name' => 'web'],
            ['slug' => 'pagination-test']
        );
        $role->givePermissionTo(Permission::firstOrCreate(['name' => 'departments.view', 'guard_name' => 'web']));
        $user->assignRole($role);
        return $user;
    }

    public function test_departments_index_has_pagination_links_with_many_records()
    {
        Department::factory(21)->create();

        $response = $this->actingAs($this->authorizedUser())
            ->get(route('departments.index'));

        $response->assertOk();
        $response->assertSee('page=2');
    }

    public function test_departments_index_shows_next_page_link()
    {
        Department::factory(25)->create();

        $response = $this->actingAs($this->authorizedUser())
            ->get(route('departments.index'));

        $response->assertOk();
        $response->assertSee('page=2');
    }
}
