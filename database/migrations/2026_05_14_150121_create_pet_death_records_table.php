<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetDeathRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('pet_death_records', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->date('death_date');
            $table->string('cause', 255)->nullable();
            $table->string('attending_vet', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('disposition', 50)->nullable();
            $table->unsignedBigInteger('registered_by')->nullable();
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets')->cascadeOnDelete();
            $table->foreign('registered_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pet_death_records');
    }
}
