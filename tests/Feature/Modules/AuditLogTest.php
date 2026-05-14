<?php

namespace Tests\Feature\Modules;

use App\Models\AuditLog;
use App\Models\Pet;
use Tests\ModuleTestCase;

class AuditLogTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('audit-logs.index'));
        $response->assertOk();
    }

    public function test_log_creation()
    {
        $pet = Pet::factory()->create();

        AuditLog::log($pet, 'created');

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => get_class($pet),
            'model_id' => $pet->id,
            'action' => 'created',
        ]);
    }
}
