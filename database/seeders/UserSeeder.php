<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Tutor;
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
        $superFinRole = Role::where('slug', 'super-financial')->first();
        $estoqueRole = Role::where('slug', 'estoque')->first();
        $hrRole = Role::where('slug', 'human-resources')->first();
        $tutorRole = Role::where('slug', 'tutor')->first();
        $auditorRole = Role::where('slug', 'auditor')->first();

        $superAdminRole = Role::where('slug', 'super-admin')->first();

        $users = [
            ['name' => 'Super Administrador', 'email' => 'super@vet.com', 'password' => 'super123', 'role' => $superAdminRole ?? $adminRole],
            ['name' => 'Administrador', 'email' => 'admin@vet.com', 'password' => 'admin123', 'role' => $adminRole],
            ['name' => 'Dr. João Silva', 'email' => 'vet@vet.com', 'password' => 'vet123', 'role' => $vetRole],
            ['name' => 'Dra. Ana Costa', 'email' => 'vet2@vet.com', 'password' => 'vet2123', 'role' => $vetRole],
            ['name' => 'Paula Recepcionista', 'email' => 'recep@vet.com', 'password' => 'recep123', 'role' => $recepRole],
            ['name' => 'Carlos Recepcionista', 'email' => 'recep2@vet.com', 'password' => 'recep2123', 'role' => $recepRole],
            ['name' => 'Carlos Financeiro', 'email' => 'financeiro@vet.com', 'password' => 'fin123', 'role' => $financeiroRole],
            ['name' => 'Daniel Super Financeiro', 'email' => 'superfin@vet.com', 'password' => 'superfin123', 'role' => $superFinRole],
            ['name' => 'Ana Estoque', 'email' => 'estoque@vet.com', 'password' => 'est123', 'role' => $estoqueRole],
            ['name' => 'Paula RH', 'email' => 'rh@vet.com', 'password' => 'rh123', 'role' => $hrRole],
            ['name' => 'Jorge Auditor', 'email' => 'auditor@vet.com', 'password' => 'auditor123', 'role' => $auditorRole],
            ['name' => 'Maria Tutor', 'email' => 'tutor@vet.com', 'password' => 'tutor123', 'role' => $tutorRole],
        ];

        $defaultBranchId = Branch::where('is_main', true)->value('id');

        foreach ($users as $user) {
            $u = User::firstOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => Hash::make($user['password']),
                    'role_id' => $user['role']->id,
                    'branch_id' => $defaultBranchId,
                    'is_active' => true,
                ]
            );

            if ($user['role']->slug === 'tutor') {
                Tutor::updateOrCreate(
                    ['user_id' => $u->id],
                    [
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'password' => Hash::make($user['password']),
                        'cpf' => '111.222.333-44',
                        'phone' => '(11) 98765-0000',
                        'address' => 'Rua dos Tutores, 100',
                        'city' => 'São Paulo',
                        'state' => 'SP',
                    ]
                );
            }
        }
    }
}
