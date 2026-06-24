<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pet_shop_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('pet_shop_packages')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->unsignedInteger('remaining_uses');
            $table->unsignedInteger('total_uses');
            $table->decimal('total_savings', 10, 2)->default(0);
            $table->enum('status', ['active', 'expired', 'cancelled', 'completed'])->default('active');
            $table->string('recurrence_rule')->nullable();
            $table->boolean('auto_renew')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pet_shop_subscriptions');
    }
};
