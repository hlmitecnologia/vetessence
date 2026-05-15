<?php

namespace Tests\Unit\Models;

use App\Models\AuditLog;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();

        AuditLog::create([
            'user_id' => $user->id,
            'model_type' => Pet::class,
            'model_id' => $pet->id,
            'action' => 'updated',
            'old_values' => ['name' => 'Old'],
            'new_values' => ['name' => 'New'],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'model_type' => Pet::class,
            'action' => 'updated',
        ]);
    }

    public function test_user_relationship()
    {
        $user = User::factory()->create();
        $pet = Pet::factory()->create();
        $log = AuditLog::create([
            'user_id' => $user->id,
            'model_type' => Pet::class,
            'model_id' => $pet->id,
            'action' => 'created',
            'old_values' => [],
            'new_values' => [],
            'ip_address' => '127.0.0.1',
            'created_at' => now(),
        ]);

        $this->assertInstanceOf(User::class, $log->user);
        $this->assertEquals($user->id, $log->user->id);
    }

    public function test_log_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $pet = Pet::factory()->create();
        AuditLog::log($pet, 'created', [], ['name' => 'Rex']);

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'model_type' => Pet::class,
            'model_id' => $pet->id,
            'action' => 'created',
        ]);
    }
}
