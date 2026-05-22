<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->char('uf', 2);
            $table->string('ibge_code', 10)->nullable();
            $table->string('country', 4)->default('BR');
            $table->timestamps();

            $table->unique(['uf', 'country']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('states');
    }
};
