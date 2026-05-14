<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVaccineProtocolsTable extends Migration
{
    public function up()
    {
        Schema::create('vaccine_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('species', 50);
            $table->string('vaccine_name', 200);
            $table->unsignedSmallInteger('age_start_weeks')->nullable();
            $table->unsignedSmallInteger('age_end_weeks')->nullable();
            $table->boolean('is_initial')->default(false);
            $table->unsignedTinyInteger('dose_number')->nullable();
            $table->unsignedSmallInteger('booster_interval_months')->nullable();
            $table->boolean('is_core')->default(true);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('vaccine_protocols');
    }
}
