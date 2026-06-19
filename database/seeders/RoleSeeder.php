<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = [
            ['name' => 'Super Administrador', 'slug' => 'super-admin'],
            ['name' => 'Administrador', 'slug' => 'admin'],
            ['name' => 'Administrador de Unidade', 'slug' => 'branch-admin'],
            ['name' => 'Veterinário', 'slug' => 'veterinario'],
            ['name' => 'Recepcionista', 'slug' => 'recepcionista'],
            ['name' => 'Financeiro', 'slug' => 'financeiro'],
            ['name' => 'Super Financeiro', 'slug' => 'super-financial'],
            ['name' => 'Estoque', 'slug' => 'estoque'],
            ['name' => 'Recursos Humanos', 'slug' => 'human-resources'],
            ['name' => 'Tutor', 'slug' => 'tutor'],
            ['name' => 'Auditor', 'slug' => 'auditor'],
            ['name' => 'Técnico', 'slug' => 'tecnico', 'description' => 'Técnico Veterinário', 'guard_name' => 'web'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
