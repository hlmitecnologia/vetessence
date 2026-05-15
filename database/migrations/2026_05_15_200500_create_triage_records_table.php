<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTriageRecordsTable extends Migration
{
    public function up()
    {
        Schema::create('triage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->dateTime('check_in_at');
            $table->enum('severity', ['green', 'yellow', 'orange', 'red'])->default('green');
            $table->text('chief_complaint')->nullable();
            $table->json('vital_signs')->nullable();
            $table->foreignId('assigned_vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('triage_vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 30)->default('waiting');
            $table->timestamp('seen_at')->nullable();
            $table->timestamp('discharged_at')->nullable();
            $table->timestamps();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('triage_records');
    }
}
