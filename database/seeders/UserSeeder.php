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

        // Admin - email: admin@vet.com, password: admin123
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@vet.com',
            'password' => Hash::make('admin123'),
            'role_id' => $adminRole->id,
            'is_active' => true,
        ]);

        // Veterinário - email: vet@vet.com, password: vet123
        User::create([
            'name' => 'Dr. João Silva',
            'email' => 'vet@vet.com',
            'password' => Hash::make('vet123'),
            'role_id' => $vetRole->id,
            'is_active' => true,
        ]);

        // Veterinário 2 - email: vet2@vet.com, password: vet2123
        User::create([
            'name' => 'Dra. Ana Costa',
            'email' => 'vet2@vet.com',
            'password' => Hash::make('vet2123'),
            'role_id' => $vetRole->id,
            'is_active' => true,
        ]);

        // Recepcionista - email: recep@vet.com, password: recep123
        User::create([
            'name' => 'Paula Recepcionista',
            'email' => 'recep@vet.com',
            'password' => Hash::make('recep123'),
            'role_id' => $recepRole->id,
            'is_active' => true,
        ]);

        // Recepcionista 2 - email: recep2@vet.com, password: recep2123
        User::create([
            'name' => 'Carlos Recepcionista',
            'email' => 'recep2@vet.com',
            'password' => Hash::make('recep2123'),
            'role_id' => $recepRole->id,
            'is_active' => true,
        ]);

        // Financeiro - email: financeiro@vet.com, password: fin123
        User::create([
            'name' => 'Carlos Financeiro',
            'email' => 'financeiro@vet.com',
            'password' => Hash::make('fin123'),
            'role_id' => $financeiroRole->id,
            'is_active' => true,
        ]);

        // Estoque - email: estoque@vet.com, password: est123
        User::create([
            'name' => 'Ana Estoque',
            'email' => 'estoque@vet.com',
            'password' => Hash::make('est123'),
            'role_id' => $estoqueRole->id,
            'is_active' => true,
        ]);

        // Tutor - email: tutor@vet.com, password: tutor123
        User::create([
            'name' => 'Maria Tutor',
            'email' => 'tutor@vet.com',
            'password' => Hash::make('tutor123'),
            'role_id' => $tutorRole->id,
            'is_active' => true,
        ]);
    }
}
