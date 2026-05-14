<?php

namespace Tests\Feature\Modules;

use App\Models\StaffSchedule;
use App\Models\User;
use Tests\ModuleTestCase;

class StaffScheduleTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('staff-schedules.index'));
        $response->assertOk();
    }

    public function test_create_schedule()
    {
        $user = User::factory()->create();

        $response = $this->post(route('staff-schedules.store'), [
            'user_id' => $user->id,
            'work_date' => now()->format('Y-m-d'),
            'start_time' => '08:00',
            'end_time' => '18:00',
            'shift_type' => 'regular',
        ]);
        $response->assertRedirect(route('staff-schedules.index'));
        $this->assertDatabaseHas('staff_schedules', ['user_id' => $user->id]);
    }
}
