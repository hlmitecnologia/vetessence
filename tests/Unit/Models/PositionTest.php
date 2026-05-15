<?php

namespace Tests\Unit\Models;

use App\Models\Position;
use App\Models\Department;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PositionTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $dept = Department::create(['name' => 'Clínica']);
        Position::create(['name' => 'Veterinário', 'description' => 'Médico veterinário', 'department_id' => $dept->id]);
        $this->assertDatabaseHas('positions', ['name' => 'Veterinário']);
    }

    public function test_department_relationship()
    {
        $dept = Department::create(['name' => 'Clínica']);
        $pos = Position::create(['name' => 'Veterinário', 'department_id' => $dept->id]);
        $this->assertInstanceOf(Department::class, $pos->department);
    }

    public function test_users_relationship()
    {
        $dept = Department::create(['name' => 'Clínica']);
        $pos = Position::create(['name' => 'Veterinário', 'department_id' => $dept->id]);
        $user = User::factory()->create(['position_id' => $pos->id]);
        $this->assertCount(1, $pos->users);
    }
}
