<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    private array $newPermissions = [
        'stock.forecast',
        'stock.reorder',
        'pet-shop-packages.view',
        'pet-shop-packages.create',
        'pet-shop-packages.edit',
        'pet-shop-packages.delete',
        'pet-shop-subscriptions.view',
        'pet-shop-subscriptions.create',
        'pet-shop-subscriptions.edit',
        'pet-shop-subscriptions.delete',
        'insurance.petlove',
    ];

    public function up(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        foreach ($this->newPermissions as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        $rolePermissions = [
            'veterinario' => [
                'pet-shop-subscriptions.view', 'pet-shop-subscriptions.create',
                'insurance.petlove',
            ],
            'recepcionista' => [
                'pet-shop-packages.view', 'pet-shop-packages.create',
                'pet-shop-subscriptions.view', 'pet-shop-subscriptions.create',
            ],
            'financeiro' => [
                'insurance.petlove',
            ],
            'estoque' => [
                'stock.forecast', 'stock.reorder',
                'pet-shop-packages.view', 'pet-shop-packages.create', 'pet-shop-packages.edit', 'pet-shop-packages.delete',
                'pet-shop-subscriptions.view', 'pet-shop-subscriptions.create', 'pet-shop-subscriptions.edit', 'pet-shop-subscriptions.delete',
            ],
        ];

        foreach ($rolePermissions as $roleSlug => $perms) {
            $role = Role::where('name', $roleSlug)->orWhere('slug', $roleSlug)->first();
            if ($role) {
                $role->givePermissionTo($perms);
            }
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', $this->newPermissions)->delete();
    }
};
