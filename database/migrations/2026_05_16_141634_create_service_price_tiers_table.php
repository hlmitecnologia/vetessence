<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicePriceTiersTable extends Migration
{
    public function up()
    {
        Schema::create('service_price_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('species', 50);
            $table->string('size', 30)->nullable();
            $table->decimal('price', 10, 2);
            $table->timestamps();

            $table->unique(['service_id', 'species', 'size']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('service_price_tiers');
    }
}
