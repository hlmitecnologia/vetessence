<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBatchTrackingToStockMovements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->string('batch_number', 100)->nullable()->after('quantity');
            $table->string('lot_number', 100)->nullable()->after('batch_number');
            $table->date('expiry_date')->nullable()->after('lot_number');
        });
    }

    public function down()
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropColumn(['batch_number', 'lot_number', 'expiry_date']);
        });
    }
}
