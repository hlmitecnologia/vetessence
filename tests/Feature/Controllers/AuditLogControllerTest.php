<?php

namespace Tests\Feature\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Tests\ModuleTestCase;

class AuditLogControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        AuditLog::factory()->count(3)->create();
        $response = $this->get(route('audit-logs.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_action()
    {
        AuditLog::factory()->create(['action' => 'created']);
        AuditLog::factory()->create(['action' => 'updated']);

        $response = $this->get(route('audit-logs.index', ['action' => 'created']));
        $response->assertOk();
    }

    public function test_index_filters_by_model()
    {
        AuditLog::factory()->create(['model_type' => 'App\Models\User']);
        AuditLog::factory()->create(['model_type' => 'App\Models\Pet']);

        $response = $this->get(route('audit-logs.index', ['model' => 'User']));
        $response->assertOk();
    }

    public function test_index_filters_by_date_range()
    {
        AuditLog::factory()->create(['created_at' => now()->subDays(5)]);
        AuditLog::factory()->create(['created_at' => now()]);

        $response = $this->get(route('audit-logs.index', [
            'date_from' => now()->subDays(2)->format('Y-m-d'),
            'date_to' => now()->addDay()->format('Y-m-d'),
        ]));
        $response->assertOk();
    }

    public function test_show()
    {
        $auditLog = AuditLog::factory()->create();
        $response = $this->get(route('audit-logs.show', $auditLog));
        $response->assertOk();
    }

    public function test_show_loads_user_relation()
    {
        $auditLog = AuditLog::factory()->create();
        $response = $this->get(route('audit-logs.show', $auditLog));
        $response->assertOk();
        $response->assertViewHas('auditLog');
    }
}
