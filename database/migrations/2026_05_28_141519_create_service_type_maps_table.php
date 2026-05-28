<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_type_maps', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['type', 'branch_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_type_maps');
    }
};
