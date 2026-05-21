<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Role as AppRole;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role as SpatieRole;

class SyncSpatieRoles extends Command
{
    protected $signature = 'roles:sync-spatie';
    protected $description = 'Sync Spatie model_has_roles from users.role_id';

    public function handle()
    {
        $users = User::whereNotNull('role_id')->get();
        $count = 0;

        foreach ($users as $user) {
            $appRole = AppRole::find($user->role_id);
            if (!$appRole) continue;

            $spatieRole = SpatieRole::where('name', $appRole->name)->first();
            if (!$spatieRole) {
                $this->warn("Spatie role not found for: {$appRole->name}");
                continue;
            }

            $exists = DB::table('model_has_roles')
                ->where('role_id', $spatieRole->id)
                ->where('model_type', get_class($user))
                ->where('model_id', $user->id)
                ->exists();

            if (!$exists) {
                DB::table('model_has_roles')->insert([
                    'role_id' => $spatieRole->id,
                    'model_type' => get_class($user),
                    'model_id' => $user->id,
                    'guard_name' => 'web',
                ]);
                $this->info("Assigned '{$spatieRole->name}' to {$user->name}");
                $count++;
            }
        }

        $this->info("Done. {$count} users synced.");
        return 0;
    }
}
