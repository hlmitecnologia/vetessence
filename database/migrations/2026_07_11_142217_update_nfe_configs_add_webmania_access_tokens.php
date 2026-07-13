<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfe_configs', function (Blueprint $table) {
            $table->string('webmania_access_token')->nullable()->after('webmania_consumer_secret');
            $table->string('webmania_access_token_secret')->nullable()->after('webmania_access_token');
        });
    }

    public function down(): void
    {
        Schema::table('nfe_configs', function (Blueprint $table) {
            $table->dropColumn(['webmania_access_token', 'webmania_access_token_secret']);
        });
    }
};
