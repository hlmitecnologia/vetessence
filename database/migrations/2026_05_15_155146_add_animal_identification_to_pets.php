<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnimalIdentificationToPets extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->string('microchip_number', 50)->nullable();
            $table->date('microchip_date')->nullable();
            $table->string('rg_number', 50)->nullable();
            $table->string('rg_issuer', 100)->nullable();
            $table->string('coat', 50)->nullable();
            $table->string('size', 20)->nullable();
        });
    }

    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['microchip_number', 'microchip_date', 'rg_number', 'rg_issuer', 'coat', 'size']);
        });
    }
}
