<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpirationDateToProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('products', 'lot_number')) {
                Schema::table('products', function (Blueprint $table) {
                    $table->date('expiration_date')->nullable()->after('lot_number');
                });
            } else {
                Schema::table('products', function (Blueprint $table) {
                    $table->date('expiration_date')->nullable();
                });
            }
    }

    public function down()
    {
        //
    }
}
