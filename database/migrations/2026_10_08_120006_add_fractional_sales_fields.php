<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->string('unit', 30)->nullable()->after('quantity');
            $table->string('pricing_mode', 20)->default('whole')->after('unit');
            $table->foreignId('inventory_batch_id')->nullable()->after('product_id')->constrained('inventory_batches')->nullOnDelete();
            $table->foreignId('inventory_container_id')->nullable()->after('inventory_batch_id')->constrained('inventory_containers')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_container_id']);
            $table->dropForeign(['inventory_batch_id']);
            $table->dropColumn(['unit', 'pricing_mode', 'inventory_batch_id', 'inventory_container_id']);
        });
    }
};
