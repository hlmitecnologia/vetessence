<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->string('municipio_ibge', 7)->nullable()->after('cnpj');
            $table->string('regime_tributario')->nullable()->after('municipio_ibge');
            $table->string('serie', 3)->nullable()->after('regime_tributario');
        });
    }

    public function down(): void
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['municipio_ibge', 'regime_tributario', 'serie']);
        });
    }
};
