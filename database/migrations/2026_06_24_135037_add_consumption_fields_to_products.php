<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('avg_daily_consumption', 10, 4)->default(0)->after('max_stock');
            $table->decimal('safety_stock', 10, 2)->default(0)->after('avg_daily_consumption');
            $table->decimal('reorder_point', 10, 2)->default(0)->after('safety_stock');
            $table->timestamp('last_consumption_calculated_at')->nullable()->after('reorder_point');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['avg_daily_consumption', 'safety_stock', 'reorder_point', 'last_consumption_calculated_at']);
        });
    }
};
