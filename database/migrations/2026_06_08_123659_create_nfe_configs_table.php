<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfe_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider');
            $table->string('ambiente')->default('homologacao');
            $table->string('focusnfe_token')->nullable();
            $table->string('nfeio_api_key')->nullable();
            $table->string('webmania_app_id')->nullable();
            $table->string('webmania_app_secret')->nullable();
            $table->string('webmania_consumer_key')->nullable();
            $table->string('webmania_consumer_secret')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfe_configs');
    }
};
