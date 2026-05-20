<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfse_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('cnpj', 18);
            $table->string('municipio_ibge', 7);
            $table->string('regime_tributario')->default('simples_nacional');
            $table->string('serie', 3)->default('1');
            $table->string('ambiente')->default('homologacao');
            $table->string('webmania_app_id');
            $table->string('webmania_app_secret');
            $table->string('webmania_consumer_key');
            $table->string('webmania_consumer_secret');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('branch_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfse_configs');
    }
};
