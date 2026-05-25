<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropUnique(['branch_id']);
            $table->dropColumn(['branch_id', 'cnpj', 'municipio_ibge', 'regime_tributario', 'serie']);
        });
    }

    public function down(): void
    {
        Schema::table('nfse_configs', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('cnpj', 18)->nullable();
            $table->string('municipio_ibge', 7)->nullable();
            $table->string('regime_tributario')->nullable();
            $table->string('serie', 3)->nullable();
        });
    }
};
