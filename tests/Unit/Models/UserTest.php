<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Position;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        User::factory()->create(['name' => 'Dr. João', 'email' => 'joao@vet.com', 'is_active' => true]);
        $this->assertDatabaseHas('users', ['email' => 'joao@vet.com', 'is_active' => true]);
    }

    public function test_is_veterinarian_cast()
    {
        $user = User::factory()->create(['is_veterinarian' => true]);
        $this->assertTrue($user->is_veterinarian);
        $this->assertIsBool($user->is_veterinarian);
    }

    public function test_is_veterinarian_defaults_to_false()
    {
        $user = User::factory()->create();
        $this->assertFalse($user->is_veterinarian);
    }

    public function test_is_veterinarian_in_fillable()
    {
        $user = User::factory()->create(['is_veterinarian' => true]);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'is_veterinarian' => 1]);
    }

    public function test_role_relationship()
    {
        $role = Role::create(['name' => 'Veterinário', 'slug' => 'veterinarian', 'guard_name' => 'web']);
        $user = User::factory()->create(['role_id' => $role->id]);
        $this->assertInstanceOf(Role::class, $user->role);
    }

    public function test_branch_relationship()
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branch->id]);
        $this->assertInstanceOf(Branch::class, $user->branch);
    }

    public function test_department_relationship()
    {
        $dept = Department::create(['name' => 'Clínica']);
        $user = User::factory()->create(['department_id' => $dept->id]);
        $this->assertInstanceOf(Department::class, $user->department);
    }

    public function test_position_relationship()
    {
        $dept = Department::create(['name' => 'Clínica']);
        $pos = Position::create(['name' => 'Veterinário', 'department_id' => $dept->id]);
        $user = User::factory()->create(['position_id' => $pos->id]);
        $this->assertInstanceOf(Position::class, $user->position);
    }
}
