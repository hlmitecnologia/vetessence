<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTherapySessionsTable extends Migration
{
    public function up()
    {
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->string('type', 50);
            $table->dateTime('session_date');
            $table->unsignedBigInteger('therapist_id')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->text('observations')->nullable();
            $table->string('status', 20)->default('scheduled');
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets')->cascadeOnDelete();
            $table->foreign('therapist_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('therapy_sessions');
    }
}
