<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('item_id')->constrained();
            $table->foreignId('service_id')->nullable()->after('product_id')->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropColumn('product_id');
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
