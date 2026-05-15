<?php

namespace Tests\Unit\Traits;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AuditableTestModel extends Model
{
    use Auditable;

    protected $table = '_test_auditable_entries';

    protected $guarded = [];
}

class AuditableTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        DB::statement('CREATE TEMPORARY TABLE IF NOT EXISTS _test_auditable_entries (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            created_at TIMESTAMP NULL,
            updated_at TIMESTAMP NULL
        ) ENGINE=InnoDB');
    }

    public function test_created_logs_audit_entry(): void
    {
        $entry = AuditableTestModel::create();

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => AuditableTestModel::class,
            'model_id' => $entry->id,
            'action' => 'created',
        ]);
    }

    public function test_updated_logs_audit_entry(): void
    {
        $entry = AuditableTestModel::create();
        $entry->update(['created_at' => now()->subHour()]);

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => AuditableTestModel::class,
            'model_id' => $entry->id,
            'action' => 'updated',
        ]);
    }

    public function test_deleted_logs_audit_entry(): void
    {
        $entry = AuditableTestModel::create();
        $id = $entry->id;
        $entry->delete();

        $this->assertDatabaseHas('audit_logs', [
            'model_type' => AuditableTestModel::class,
            'model_id' => $id,
            'action' => 'deleted',
        ]);
    }
}
