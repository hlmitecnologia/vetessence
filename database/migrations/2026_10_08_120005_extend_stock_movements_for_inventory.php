<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->decimal('quantity', 14, 4)->change();
            $table->decimal('balance_after', 14, 4)->change();
            $table->foreignId('inventory_batch_id')->nullable()->after('product_id')->constrained('inventory_batches')->nullOnDelete();
            $table->foreignId('inventory_container_id')->nullable()->after('inventory_batch_id')->constrained('inventory_containers')->nullOnDelete();
            $table->string('unit', 30)->nullable()->after('quantity');
            $table->string('movement_reason', 50)->nullable()->after('notes');
            $table->string('idempotency_key', 100)->nullable()->unique();
        });
    }

    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropUnique(['idempotency_key']);
            $table->dropForeign(['inventory_container_id']);
            $table->dropForeign(['inventory_batch_id']);
            $table->dropColumn(['inventory_container_id', 'inventory_batch_id', 'unit', 'movement_reason', 'idempotency_key']);
            $table->integer('quantity')->change();
            $table->integer('balance_after')->change();
        });
    }
};
