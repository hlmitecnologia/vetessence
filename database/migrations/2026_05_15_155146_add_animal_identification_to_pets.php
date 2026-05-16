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
            $table->string('microchip_number', 50)->nullable()->after('color');
            $table->date('microchip_date')->nullable()->after('microchip_number');
            $table->string('rg_number', 50)->nullable()->after('microchip_date');
            $table->string('rg_issuer', 100)->nullable()->after('rg_number');
            $table->string('coat', 50)->nullable()->after('rg_issuer');
            $table->string('size', 20)->nullable()->after('coat');
        });
    }

    public function down()
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropColumn(['microchip_number', 'microchip_date', 'rg_number', 'rg_issuer', 'coat', 'size']);
        });
    }
}
