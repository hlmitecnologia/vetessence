<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOnlineBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('online_bookings', function (Blueprint $table) {
            $table->id();
            $table->string('tutor_name', 255);
            $table->string('tutor_email', 255);
            $table->string('tutor_phone', 50);
            $table->string('pet_name', 255);
            $table->string('pet_species', 50);
            $table->string('pet_breed', 100)->nullable();
            $table->date('preferred_date');
            $table->string('preferred_time', 20)->nullable();
            $table->string('reason', 255)->nullable();
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending'); // pending, confirmed, rejected, cancelled, converted
            $table->foreignId('converted_appointment_id')->nullable()->constrained('appointments');
            $table->text('staff_notes')->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users');
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('online_bookings');
    }
}
