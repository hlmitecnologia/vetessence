<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('branches', 'ie')) {
            Schema::table('branches', function (Blueprint $table): void {
                $table->string('ie', 20)->nullable()->after('cnpj');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('branches', 'ie')) {
            Schema::table('branches', function (Blueprint $table): void {
                $table->dropColumn('ie');
            });
        }
    }
};
