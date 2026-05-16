<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Service;
use App\Models\ServicePriceTier;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ServicePriceTierTest extends TestCase
{
    use DatabaseTransactions;

    public function test_service_create_with_tiers()
    {
        $admin = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin-test-tiers', 'guard_name' => 'web']);
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.create', 'guard_name' => 'web']));
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.view', 'guard_name' => 'web']));
        $admin->assignRole($role);

        $this->actingAs($admin)->post(route('services.store'), [
            'name' => 'Banho',
            'price' => 50,
            'tiers' => [
                ['species' => 'Canina', 'size' => 'Pequeno', 'price' => 40],
                ['species' => 'Canina', 'size' => 'Grande', 'price' => 80],
            ],
        ])->assertRedirect();

        $service = Service::where('name', 'Banho')->first();
        $this->assertNotNull($service);
        $this->assertCount(2, $service->priceTiers);
    }

    public function test_service_edit_updates_tiers()
    {
        $admin = User::factory()->create();
        $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin-test-tiers2', 'guard_name' => 'web']);
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.edit', 'guard_name' => 'web']));
        $role->givePermissionTo(\Spatie\Permission\Models\Permission::firstOrCreate(['name' => 'services.view', 'guard_name' => 'web']));
        $admin->assignRole($role);

        $service = Service::factory()->create(['name' => 'Tosa', 'price' => 60]);
        $service->priceTiers()->create(['species' => 'Felina', 'price' => 50]);

        $this->actingAs($admin)->put(route('services.update', $service), [
            'name' => 'Tosa Premium',
            'price' => 70,
            'is_active' => 1,
            'tiers' => [
                ['species' => 'Felina', 'price' => 60],
                ['species' => 'Canina', 'size' => 'Grande', 'price' => 90],
            ],
        ])->assertRedirect();

        $this->assertEquals(2, $service->fresh()->priceTiers->count());
    }
}
