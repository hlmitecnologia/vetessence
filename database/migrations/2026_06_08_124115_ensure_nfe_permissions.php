<?php

use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    public function up(): void
    {
        foreach (['nfe.emit', 'nfe.cancel', 'nfse.emit', 'nfse.cancel'] as $perm) {
            Permission::findOrCreate($perm, 'web');
        }
    }

    public function down(): void
    {
        Permission::whereIn('name', ['nfe.emit', 'nfe.cancel', 'nfse.emit', 'nfse.cancel'])
            ->where('guard_name', 'web')->delete();
    }
};
