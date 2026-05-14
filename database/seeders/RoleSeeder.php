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
            ['name' => 'Administrador', 'slug' => 'admin'],
            ['name' => 'Veterinário', 'slug' => 'veterinario'],
            ['name' => 'Recepcionista', 'slug' => 'recepcionista'],
            ['name' => 'Financeiro', 'slug' => 'financeiro'],
            ['name' => 'Estoque', 'slug' => 'estoque'],
            ['name' => 'Tutor', 'slug' => 'tutor'],
        ];

        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
