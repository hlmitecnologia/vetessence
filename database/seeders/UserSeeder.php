<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $adminRole = Role::where('slug', 'admin')->first();
        $vetRole = Role::where('slug', 'veterinario')->first();
        $recepRole = Role::where('slug', 'recepcionista')->first();
        $financeiroRole = Role::where('slug', 'financeiro')->first();
        $estoqueRole = Role::where('slug', 'estoque')->first();
        $tutorRole = Role::where('slug', 'tutor')->first();

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $users = [
            ['name' => 'Super Administrador', 'email' => 'super@vet.com', 'password' => 'super123', 'role' => $superAdminRole ?? $adminRole],
            ['name' => 'Administrador', 'email' => 'admin@vet.com', 'password' => 'admin123', 'role' => $adminRole],
            ['name' => 'Dr. João Silva', 'email' => 'vet@vet.com', 'password' => 'vet123', 'role' => $vetRole],
            ['name' => 'Dra. Ana Costa', 'email' => 'vet2@vet.com', 'password' => 'vet2123', 'role' => $vetRole],
            ['name' => 'Paula Recepcionista', 'email' => 'recep@vet.com', 'password' => 'recep123', 'role' => $recepRole],
            ['name' => 'Carlos Recepcionista', 'email' => 'recep2@vet.com', 'password' => 'recep2123', 'role' => $recepRole],
            ['name' => 'Carlos Financeiro', 'email' => 'financeiro@vet.com', 'password' => 'fin123', 'role' => $financeiroRole],
            ['name' => 'Ana Estoque', 'email' => 'estoque@vet.com', 'password' => 'est123', 'role' => $estoqueRole],
            ['name' => 'Maria Tutor', 'email' => 'tutor@vet.com', 'password' => 'tutor123', 'role' => $tutorRole],
        ];

        foreach ($users as $user) {
            User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role_id' => $user['role']->id,
                    'is_active' => true,
                ]
            );
        }
    }
}
