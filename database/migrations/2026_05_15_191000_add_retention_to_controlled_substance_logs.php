<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRetentionToControlledSubstanceLogs extends Migration
{
    public function up()
    {
        Schema::table('controlled_substance_logs', function (Blueprint $table) {
            $table->date('retention_until')->nullable()->after('notes');
            $table->boolean('is_archived')->default(false)->after('retention_until');
        });

        DB::statement('UPDATE controlled_substance_logs SET retention_until = DATE_ADD(created_at, INTERVAL 2 YEAR) WHERE retention_until IS NULL');
    }

    public function down()
    {
        Schema::table('controlled_substance_logs', function (Blueprint $table) {
            $table->dropColumn(['retention_until', 'is_archived']);
        });
    }
}
