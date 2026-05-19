<?php

namespace Tests\Unit\Traits;

use App\Models\Appointment;
use App\Scopes\BranchScope;
use App\Traits\BranchScoped;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BranchScopedTest extends TestCase
{
    use DatabaseTransactions;

    public function test_global_scope_is_registered(): void
    {
        $scopes = (new Appointment)->getGlobalScopes();
        $this->assertArrayHasKey(BranchScope::class, $scopes);
    }

    public function test_branch_relationship(): void
    {
        $model = new Appointment();
        $relation = $model->branch();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class, $relation);
        $this->assertEquals('branch_id', $relation->getForeignKeyName());
    }

    public function test_scope_for_branch(): void
    {
        $query = Appointment::query();
        $result = (new Appointment())->scopeForBranch($query, 5);

        $this->assertSame($query, $result);
    }

    public function test_without_branch_removes_global_scope(): void
    {
        $query = Appointment::withoutBranch();

        $this->assertEmpty($query->getQuery()->wheres);
    }
}
