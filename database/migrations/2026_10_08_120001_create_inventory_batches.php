<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->string('batch_number', 100)->nullable();
            $table->string('lot_number', 100)->nullable();
            $table->date('expiration_date')->nullable();
            $table->decimal('quantity_received', 14, 4)->default(0);
            $table->decimal('quantity_available', 14, 4)->default(0);
            $table->string('unit', 30)->default('un');
            $table->decimal('package_quantity', 14, 4)->default(1);
            $table->decimal('package_count', 14, 4)->default(0);
            $table->decimal('unit_cost', 12, 4)->nullable();
            $table->string('status', 30)->default('active');
            $table->boolean('is_legacy')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'branch_id', 'status']);
            $table->index(['expiration_date', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_batches');
    }
};
