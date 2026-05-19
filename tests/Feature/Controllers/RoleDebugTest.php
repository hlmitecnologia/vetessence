<?php
// Simple test to check what happens
namespace Tests\Feature\Controllers;

use App\Models\Role;
use Tests\ModuleTestCase;

class RoleDebugTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_debug_update()
    {
        $this->markTestSkipped('Debug test — no assertions.');
    }
}
