<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class NFSeGateTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        if (Role::count() === 0) {
            Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        }
        Permission::create(['name' => 'nfse.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'nfse.emit', 'guard_name' => 'web']);
        Permission::create(['name' => 'nfse.cancel', 'guard_name' => 'web']);
        Permission::create(['name' => 'nfse-config.edit', 'guard_name' => 'web']);
        $this->user = User::factory()->create(['is_active' => true]);
    }

    public function test_guest_cannot_view_nfse()
    {
        $this->get(route('nfse.index'))->assertRedirect(route('login'));
    }

    public function test_user_without_permission_gets_denied()
    {
        $this->assertFalse(Gate::forUser($this->user)->allows('nfse.view'));
    }

    public function test_user_with_permission_can_view_nfse()
    {
        $this->user->givePermissionTo('nfse.view');
        $this->assertTrue(Gate::forUser($this->user)->allows('nfse.view'));
    }

    public function test_user_with_permission_can_emit_nfse()
    {
        $this->user->givePermissionTo('nfse.emit');
        $this->assertTrue(Gate::forUser($this->user)->allows('nfse.emit'));
    }

    public function test_super_admin_can_bypass_nfse_gates()
    {
        $this->user->assignRole('super-admin');
        $this->assertTrue(Gate::forUser($this->user)->allows('nfse.view'));
        $this->assertTrue(Gate::forUser($this->user)->allows('nfse.emit'));
    }
}
