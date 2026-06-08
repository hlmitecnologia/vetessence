<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->string('service_code', 20)->nullable()->after('description');
            $table->string('cnae', 7)->nullable()->after('service_code');
            $table->decimal('iss_aliquot', 5, 2)->nullable()->after('cnae');
            $table->string('iss_municipio_ibge', 7)->nullable()->after('iss_aliquot');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['service_code', 'cnae', 'iss_aliquot', 'iss_municipio_ibge']);
        });
    }
};
