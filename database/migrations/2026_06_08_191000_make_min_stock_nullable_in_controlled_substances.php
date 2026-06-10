<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE controlled_substances MODIFY min_stock DECIMAL(10,2) NULL DEFAULT 0');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE controlled_substances MODIFY min_stock DECIMAL(10,2) NOT NULL DEFAULT 0');
    }
};
