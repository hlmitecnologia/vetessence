<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->dropColumn(['ginfes_username', 'ginfes_password']);
            $table->string('spedy_api_key')->nullable()->after('focusnfe_token');
            $table->string('spedy_api_secret')->nullable()->after('spedy_api_key');
            $table->string('nfeio_api_key')->nullable()->after('spedy_api_secret');
        });
    }

    public function down(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->dropColumn(['spedy_api_key', 'spedy_api_secret', 'nfeio_api_key']);
            $table->string('ginfes_username')->nullable();
            $table->string('ginfes_password')->nullable();
        });
    }
};
