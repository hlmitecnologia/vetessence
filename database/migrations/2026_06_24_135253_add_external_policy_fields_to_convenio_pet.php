<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('convenio_pet', function (Blueprint $table) {
            $table->string('external_policy_id')->nullable()->after('end_date');
            $table->timestamp('eligibility_last_checked_at')->nullable()->after('external_policy_id');
        });
    }

    public function down(): void
    {
        Schema::table('convenio_pet', function (Blueprint $table) {
            $table->dropColumn(['external_policy_id', 'eligibility_last_checked_at']);
        });
    }
};
