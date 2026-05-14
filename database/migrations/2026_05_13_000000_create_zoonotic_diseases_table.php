<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('zoonotic_diseases', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category', 50); // viral, bacterial, parasitic, fungal, prion
            $table->string('causative_agent')->nullable();
            $table->text('transmission')->nullable();
            $table->text('animal_symptoms')->nullable();
            $table->text('human_symptoms')->nullable();
            $table->string('incubation_period', 100)->nullable();
            $table->text('prevention')->nullable();
            $table->text('treatment')->nullable();
            $table->boolean('is_notifiable')->default(false);
            $table->json('species_affected')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('diagnosis_disease', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();
            $table->foreignId('zoonotic_disease_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_suspected')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['medical_record_id', 'zoonotic_disease_id'], 'diagnosis_disease_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('diagnosis_disease');
        Schema::dropIfExists('zoonotic_diseases');
    }
};
