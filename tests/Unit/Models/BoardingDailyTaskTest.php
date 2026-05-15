<?php

namespace Tests\Unit\Models;

use App\Models\BoardingDailyTask;
use App\Models\Boarding;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BoardingDailyTaskTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $boarding = Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now(), 'status' => 'checked_in', 'created_by' => $user->id]);
        BoardingDailyTask::create([
            'boarding_id' => $boarding->id, 'task_date' => now(), 'task_name' => 'Alimentar',
            'is_completed' => false,
        ]);
        $this->assertDatabaseHas('boarding_daily_tasks', ['boarding_id' => $boarding->id, 'task_name' => 'Alimentar']);
    }

    public function test_boarding_relationship()
    {
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $boarding = Boarding::create(['pet_id' => $pet->id, 'type' => 'standard', 'check_in_at' => now(), 'status' => 'checked_in', 'created_by' => $user->id]);
        $task = BoardingDailyTask::create(['boarding_id' => $boarding->id, 'task_date' => now(), 'task_name' => 'Alimentar']);
        $this->assertInstanceOf(Boarding::class, $task->boarding);
    }
}
