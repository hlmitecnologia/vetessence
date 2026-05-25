<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->string('provider', 50)->default('webmania')->after('is_active');
            $table->string('focusnfe_token')->nullable()->after('webmania_consumer_secret');
            $table->string('ginfes_username')->nullable()->after('focusnfe_token');
            $table->string('ginfes_password')->nullable()->after('ginfes_username');
        });
    }

    public function down(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->dropColumn(['provider', 'focusnfe_token', 'ginfes_username', 'ginfes_password']);
        });
    }
};
