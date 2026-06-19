<?php

namespace Tests\Unit\Models;

use App\Models\ExecutionLog;
use App\Models\ExecutionTask;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ExecutionLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $task = ExecutionTask::factory()->create();
        $user = User::factory()->create();
        ExecutionLog::create([
            'execution_task_id' => $task->id,
            'performed_at' => now(),
            'performed_by' => $user->id,
            'status' => 'completed',
            'notes' => 'Done',
        ]);
        $this->assertDatabaseHas('execution_logs', ['execution_task_id' => $task->id, 'status' => 'completed']);
    }

    public function test_task_relationship()
    {
        $log = ExecutionLog::factory()->create();
        $this->assertInstanceOf(ExecutionTask::class, $log->task);
    }

    public function test_performer_relationship()
    {
        $log = ExecutionLog::factory()->create();
        $this->assertInstanceOf(User::class, $log->performer);
    }

    public function test_performed_at_cast()
    {
        $log = ExecutionLog::factory()->create();
        $this->assertInstanceOf(\Carbon\Carbon::class, $log->performed_at);
    }
}
