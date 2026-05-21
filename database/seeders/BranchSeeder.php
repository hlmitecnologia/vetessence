<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $branch = Branch::firstOrCreate(
            ['slug' => 'matriz'],
            [
                'name' => 'Matriz',
                'address' => 'Rua Principal, 100',
                'city' => 'São Paulo',
                'state' => 'SP',
                'phone' => '(11) 3000-0000',
                'email' => 'matriz@vetessence.com.br',
                'cnpj' => '00.000.000/0001-00',
                'is_active' => true,
                'is_main' => true,
            ]
        );

        User::whereNull('branch_id')->update(['branch_id' => $branch->id]);
    }
}
