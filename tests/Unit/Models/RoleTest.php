<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class RoleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Role::create(['name' => 'Veterinário', 'slug' => 'veterinarian', 'description' => 'Médico veterinário', 'guard_name' => 'web']);
        $this->assertDatabaseHas('roles', ['slug' => 'veterinarian']);
    }

    public function test_users_relationship()
    {
        $role = Role::create(['name' => 'Veterinário', 'slug' => 'veterinarian', 'guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->assertCount(1, $role->users);
    }
}
