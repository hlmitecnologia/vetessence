<?php

namespace Tests\Unit\Models;

use App\Models\ExecutionTask;
use App\Models\ExecutionLog;
use App\Models\ExecutionMap;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExecutionTaskTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $map = ExecutionMap::factory()->create();
        $user = User::factory()->create();
        ExecutionTask::create([
            'execution_map_id' => $map->id,
            'title' => 'Test task',
            'category' => 'medication',
            'status' => 'pending',
            'created_by' => $user->id,
        ]);
        $this->assertDatabaseHas('execution_tasks', ['title' => 'Test task']);
    }

    public function test_execution_map_relationship()
    {
        $task = ExecutionTask::factory()->create();
        $this->assertInstanceOf(ExecutionMap::class, $task->executionMap);
    }

    public function test_logs_relationship()
    {
        $task = ExecutionTask::factory()->create();
        ExecutionLog::factory()->create(['execution_task_id' => $task->id]);
        $this->assertCount(1, $task->logs);
    }

    public function test_creator_relationship()
    {
        $task = ExecutionTask::factory()->create();
        $this->assertInstanceOf(User::class, $task->creator);
    }

    public function test_pending_scope()
    {
        ExecutionTask::factory()->create(['status' => 'pending']);
        ExecutionTask::factory()->completed()->create();
        $this->assertCount(1, ExecutionTask::pending()->get());
    }

    public function test_parse_frequency_every_8h()
    {
        $this->assertEquals([6, 14, 22], ExecutionTask::parseFrequency('every_8h'));
        $this->assertEquals([6, 14, 22], ExecutionTask::parseFrequency('8/8h'));
        $this->assertEquals([6, 14, 22], ExecutionTask::parseFrequency('TID'));
    }

    public function test_parse_frequency_every_12h()
    {
        $this->assertEquals([8, 20], ExecutionTask::parseFrequency('every_12h'));
        $this->assertEquals([8, 20], ExecutionTask::parseFrequency('12/12h'));
        $this->assertEquals([8, 20], ExecutionTask::parseFrequency('BID'));
    }

    public function test_parse_frequency_every_6h()
    {
        $this->assertEquals([0, 6, 12, 18], ExecutionTask::parseFrequency('every_6h'));
        $this->assertEquals([0, 6, 12, 18], ExecutionTask::parseFrequency('6/6h'));
        $this->assertEquals([0, 6, 12, 18], ExecutionTask::parseFrequency('QID'));
    }

    public function test_parse_frequency_daily()
    {
        $this->assertEquals([8], ExecutionTask::parseFrequency('every_24h'));
        $this->assertEquals([8], ExecutionTask::parseFrequency('SID'));
        $this->assertEquals([8], ExecutionTask::parseFrequency('daily'));
    }

    public function test_parse_frequency_fallback()
    {
        $this->assertEquals([8], ExecutionTask::parseFrequency('unknown_frequency'));
    }
}
