<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;

return new class extends Migration
{
    public function up(): void
    {
        $perms = [
            Permission::findOrCreate('staff-notes.view', 'web'),
            Permission::findOrCreate('staff-notes.create', 'web'),
            Permission::findOrCreate('chat.view', 'web'),
            Permission::findOrCreate('chat.create', 'web'),
        ];

        $roles = SpatieRole::whereIn('name', [
            'Financeiro', 'Super Financeiro', 'Estoque', 'Recursos Humanos', 'Tutor',
        ])->get();

        foreach ($roles as $role) {
            $role->givePermissionTo($perms);
        }
    }

    public function down(): void
    {
        $roles = SpatieRole::whereIn('name', [
            'Financeiro', 'Super Financeiro', 'Estoque', 'Recursos Humanos', 'Tutor',
        ])->get();

        foreach (['staff-notes.view', 'staff-notes.create', 'chat.view', 'chat.create'] as $perm) {
            foreach ($roles as $role) {
                $role->revokePermissionTo($perm);
            }
        }
    }
};
