<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE model_has_roles MODIFY COLUMN guard_name VARCHAR(255) NOT NULL DEFAULT "web"');
        DB::statement('ALTER TABLE model_has_permissions MODIFY COLUMN guard_name VARCHAR(255) NOT NULL DEFAULT "web"');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE model_has_roles MODIFY COLUMN guard_name VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE model_has_permissions MODIFY COLUMN guard_name VARCHAR(255) NOT NULL');
    }
};
