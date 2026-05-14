<?php

namespace Tests\Unit\Models;

use App\Models\StaffTimeOff;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StaffTimeOffTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $user = User::factory()->create();
        StaffTimeOff::create([
            'user_id' => $user->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'ferias',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('staff_time_off', [
            'user_id' => $user->id,
            'type' => 'ferias',
            'status' => 'pending',
        ]);
    }

    public function test_pending_scope()
    {
        $user = User::factory()->create();
        StaffTimeOff::create([
            'user_id' => $user->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'ferias',
            'status' => 'pending',
        ]);
        StaffTimeOff::create([
            'user_id' => $user->id,
            'start_date' => now(),
            'end_date' => now()->addDays(5),
            'type' => 'ferias',
            'status' => 'approved',
        ]);

        $this->assertCount(1, StaffTimeOff::pending()->get());
    }
}
