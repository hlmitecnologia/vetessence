<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->string('webmania_access_token')->nullable()->after('webmania_consumer_secret');
        });
    }

    public function down(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->dropColumn('webmania_access_token');
        });
    }
};
