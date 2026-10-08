<?php

namespace Tests\Feature\Seeders;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class PermissionSeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_permission_seeder_creates_permissions_when_roles_require_slug(): void
    {
        $this->seed(\Database\Seeders\PermissionSeeder::class);

        $this->assertTrue(Permission::where('name', 'nfe.view')->where('guard_name', 'web')->exists());
        $this->assertTrue(Permission::where('name', 'docs.view')->where('guard_name', 'web')->exists());
        $this->assertTrue(Permission::where('name', 'stock.transfer')->where('guard_name', 'web')->exists());
    }
}
