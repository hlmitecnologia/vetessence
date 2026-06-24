<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_shop_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['grooming', 'boarding', 'both'])->default('grooming');
            $table->json('services');
            $table->decimal('total_price', 10, 2);
            $table->decimal('original_price', 10, 2);
            $table->unsignedInteger('validity_days')->default(30);
            $table->unsignedInteger('max_uses')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_shop_packages');
    }
};
