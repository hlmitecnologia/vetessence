<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfe_configs', function (Blueprint $table) {
            $table->string('nfeio_company_id')->nullable()->after('nfeio_api_key');
        });

        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->string('nfeio_company_id')->nullable()->after('nfeio_api_key');
        });
    }

    public function down(): void
    {
        Schema::table('nfe_configs', function (Blueprint $table) {
            $table->dropColumn('nfeio_company_id');
        });

        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->dropColumn('nfeio_company_id');
        });
    }
};
