<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_shop_consumptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained('pet_shop_subscriptions')->cascadeOnDelete();
            $table->foreignId('boarding_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->date('service_date');
            $table->foreignId('used_by')->constrained('users');
            $table->decimal('savings_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_shop_consumptions');
    }
};
