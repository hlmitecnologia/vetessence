<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreAnestheticEvaluationsTable extends Migration
{
    public function up()
    {
        Schema::create('pre_anesthetic_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('surgery_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vet_id')->constrained('users')->cascadeOnDelete();
            $table->enum('asa_score', [1, 2, 3, 4, 5, 6]);
            $table->boolean('fasted')->default(false);
            $table->boolean('hydrated')->default(false);
            $table->json('exam_checklist')->nullable();
            $table->text('observations')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('recommendations')->nullable();
            $table->timestamps();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pre_anesthetic_evaluations');
    }
}
