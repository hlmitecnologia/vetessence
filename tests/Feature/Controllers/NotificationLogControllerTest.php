<?php

namespace Tests\Feature\Controllers;

use App\Models\NotificationLog;
use Tests\ModuleTestCase;

class NotificationLogControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('notification-logs.index'));
        $response->assertOk();
    }

    public function test_show()
    {
        $log = NotificationLog::factory()->create();

        $response = $this->get(route('notification-logs.show', $log));
        $response->assertOk();
    }

    public function test_destroy()
    {
        $log = NotificationLog::factory()->create();

        $response = $this->delete(route('notification-logs.destroy', $log));
        $response->assertRedirect(route('notification-logs.index'));
        $this->assertDatabaseMissing('notification_logs', ['id' => $log->id]);
    }
}
