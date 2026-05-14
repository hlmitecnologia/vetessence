<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeleconsultationsTable extends Migration
{
    public function up()
    {
        Schema::create('teleconsultations', function (Blueprint $table) {
            $table->id();
            $table->string('room_name', 255);
            $table->string('room_token', 100)->unique();
            $table->foreignId('appointment_id')->nullable()->constrained();
            $table->foreignId('pet_id')->constrained();
            $table->foreignId('tutor_id')->nullable()->constrained('users');
            $table->foreignId('vet_id')->constrained('users');
            $table->string('status', 20)->default('scheduled'); // scheduled, active, completed, cancelled
            $table->string('provider', 50)->default('jitsi'); // jitsi, zoom, google_meet, other
            $table->string('provider_room_id', 255)->nullable();
            $table->string('provider_url', 500)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->integer('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->text('recording_url')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('teleconsultations');
    }
}
