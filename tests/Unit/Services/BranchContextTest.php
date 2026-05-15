<?php

namespace Tests\Unit\Services;

use App\Services\BranchContext;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BranchContextTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        BranchContext::clear();
    }

    public function test_set_and_get_branch(): void
    {
        BranchContext::set(1);
        $this->assertEquals(1, BranchContext::get());
    }

    public function test_set_null_makes_global(): void
    {
        BranchContext::set(null);
        $this->assertNull(BranchContext::get());
        $this->assertTrue(BranchContext::isGlobal());
        $this->assertFalse(BranchContext::hasBranch());
    }

    public function test_is_global(): void
    {
        BranchContext::set(1);
        $this->assertFalse(BranchContext::isGlobal());

        BranchContext::set(null);
        $this->assertTrue(BranchContext::isGlobal());
    }

    public function test_has_branch(): void
    {
        BranchContext::set(1);
        $this->assertTrue(BranchContext::hasBranch());

        BranchContext::set(null);
        $this->assertFalse(BranchContext::hasBranch());
    }

    public function test_clear_resets_state(): void
    {
        BranchContext::set(1);
        BranchContext::clear();

        $this->assertNull(BranchContext::get());
        $this->assertFalse(BranchContext::isGlobal());
        $this->assertFalse(BranchContext::hasBranch());
    }
}
