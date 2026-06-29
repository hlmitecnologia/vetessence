<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->enum('channel', ['portal', 'pdv', 'both'])
                ->default('both')
                ->after('provider');
        });
    }

    public function down()
    {
        Schema::table('payment_gateways', function (Blueprint $table) {
            $table->dropColumn('channel');
        });
    }
};
