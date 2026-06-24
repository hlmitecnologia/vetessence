<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $mainBranch = DB::table('branches')->where('is_main', true)->value('id');

        if ($mainBranch) {
            DB::table('appointments')->whereNull('branch_id')->update(['branch_id' => $mainBranch]);
        }
    }

    public function down(): void
    {
        // Não é possível reverter — branch_id null não é destrutivo
    }
};
