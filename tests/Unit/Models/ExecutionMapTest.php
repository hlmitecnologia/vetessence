<?php

namespace Tests\Unit\Models;

use App\Models\ExecutionMap;
use App\Models\ExecutionTask;
use App\Models\Hospitalization;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExecutionMapTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $hospitalization = Hospitalization::factory()->create();
        $user = User::factory()->create();
        ExecutionMap::create([
            'hospitalization_id' => $hospitalization->id,
            'date' => '2026-06-19',
            'notes' => 'Test notes',
            'created_by' => $user->id,
        ]);
        $this->assertDatabaseHas('execution_maps', ['hospitalization_id' => $hospitalization->id, 'date' => '2026-06-19']);
    }

    public function test_date_cast()
    {
        $map = ExecutionMap::factory()->create(['date' => '2026-06-19']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $map->date);
    }

    public function test_hospitalization_relationship()
    {
        $map = ExecutionMap::factory()->create();
        $this->assertInstanceOf(Hospitalization::class, $map->hospitalization);
    }

    public function test_tasks_relationship()
    {
        $map = ExecutionMap::factory()->create();
        ExecutionTask::factory()->create(['execution_map_id' => $map->id]);
        $this->assertCount(1, $map->tasks);
    }

    public function test_creator_relationship()
    {
        $map = ExecutionMap::factory()->create();
        $this->assertInstanceOf(User::class, $map->creator);
    }

    public function test_unique_hospitalization_date()
    {
        $hospitalization = Hospitalization::factory()->create();
        ExecutionMap::factory()->create([
            'hospitalization_id' => $hospitalization->id,
            'date' => '2026-06-19',
        ]);
        $this->expectException(\Illuminate\Database\QueryException::class);
        ExecutionMap::factory()->create([
            'hospitalization_id' => $hospitalization->id,
            'date' => '2026-06-19',
        ]);
    }
}
