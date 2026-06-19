<?php

use App\Models\Branch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role as SpatieRole;

return new class extends Migration
{
    public function up()
    {
        $tecnicoRole = Role::firstOrCreate(
            ['slug' => 'tecnico'],
            [
                'name' => 'Técnico',
                'description' => 'Técnico Veterinário',
                'guard_name' => 'web',
            ]
        );

        $spatieRole = SpatieRole::find($tecnicoRole->id);
        if (!$spatieRole) {
            SpatieRole::create([
                'id' => $tecnicoRole->id,
                'name' => 'tecnico',
                'guard_name' => 'web',
            ]);
        }

        $branchId = Branch::where('is_main', true)->value('id');
        if (!$branchId) {
            $branchId = Branch::value('id');
        }
        if (!$branchId) {
            return;
        }

        User::firstOrCreate(
            ['email' => 'tecnico@vet.com'],
            [
                'name' => 'Carlos Técnico',
                'password' => Hash::make('tecnico123'),
                'role_id' => $tecnicoRole->id,
                'branch_id' => $branchId,
                'is_active' => true,
                'is_veterinarian' => false,
            ]
        );
    }

    public function down()
    {
        User::where('email', 'tecnico@vet.com')->delete();
    }
};
