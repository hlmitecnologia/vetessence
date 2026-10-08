<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('stock_unit', 30)->default('un')->after('unit');
            $table->string('dispensing_unit', 30)->nullable()->after('stock_unit');
            $table->decimal('package_quantity', 14, 4)->default(1)->after('dispensing_unit');
            $table->decimal('fractional_cost_price', 12, 4)->nullable()->after('sale_price');
            $table->decimal('fractional_sale_price', 12, 4)->nullable()->after('fractional_cost_price');
            $table->boolean('allows_fractional')->default(false)->after('fractional_sale_price');
            $table->boolean('requires_container_tracking')->default(false)->after('allows_fractional');
            $table->unsignedSmallInteger('opened_use_days')->nullable()->after('requires_container_tracking');
            $table->unsignedSmallInteger('reconstituted_use_days')->nullable()->after('opened_use_days');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'stock_unit', 'dispensing_unit', 'package_quantity',
                'fractional_cost_price', 'fractional_sale_price',
                'allows_fractional', 'requires_container_tracking',
                'opened_use_days', 'reconstituted_use_days',
            ]);
        });
    }
};
