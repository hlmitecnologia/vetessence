<?php

namespace Tests\Unit\Models;

use App\Models\Department;
use App\Models\Position;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DepartmentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Department::create(['name' => 'Clínica Geral', 'description' => 'Atendimento geral']);
        $this->assertDatabaseHas('departments', ['name' => 'Clínica Geral']);
    }

    public function test_positions_relationship()
    {
        $dept = Department::create(['name' => 'Clínica']);
        $position = Position::create(['name' => 'Veterinário', 'department_id' => $dept->id]);
        $this->assertCount(1, $dept->positions);
    }

    public function test_users_relationship()
    {
        $dept = Department::create(['name' => 'Clínica']);
        $user = User::factory()->create(['department_id' => $dept->id]);
        $this->assertCount(1, $dept->users);
    }
}
