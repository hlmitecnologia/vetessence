<?php

namespace Tests\Unit\Models;

use App\Models\StaffSchedule;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class StaffScheduleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $user = User::factory()->create();
        StaffSchedule::create([
            'user_id' => $user->id,
            'work_date' => now(),
            'start_time' => '08:00',
            'end_time' => '18:00',
        ]);

        $this->assertDatabaseHas('staff_schedules', [
            'user_id' => $user->id,
            'start_time' => '08:00',
            'end_time' => '18:00',
        ]);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $schedule = StaffSchedule::create([
            'user_id' => $user->id,
            'work_date' => now(),
            'start_time' => '08:00',
            'end_time' => '18:00',
        ]);

        $this->assertInstanceOf(BelongsTo::class, $schedule->user());
    }

    public function test_is_vet_shift_defaults_to_false()
    {
        $schedule = StaffSchedule::factory()->create();

        $this->assertFalse($schedule->fresh()->is_vet_shift);
    }

    public function test_is_vet_shift_can_be_set_to_true()
    {
        $schedule = StaffSchedule::factory()->create([
            'is_vet_shift' => true,
        ]);

        $this->assertTrue($schedule->is_vet_shift);
    }

    public function test_vet_shift_is_fillable()
    {
        $user = User::factory()->create();
        StaffSchedule::create([
            'user_id' => $user->id,
            'work_date' => now(),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'is_vet_shift' => true,
        ]);

        $this->assertDatabaseHas('staff_schedules', [
            'user_id' => $user->id,
            'is_vet_shift' => true,
        ]);
    }
}
