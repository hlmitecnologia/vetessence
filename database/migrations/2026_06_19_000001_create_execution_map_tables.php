<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('execution_maps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->unique(['hospitalization_id', 'date']);
        });

        Schema::create('execution_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('execution_map_id')->constrained()->cascadeOnDelete();
            $table->enum('category', ['medication', 'procedure', 'exam', 'care', 'other'])->default('medication');
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->string('frequency', 50)->nullable();
            $table->string('route', 50)->nullable();
            $table->string('dosage', 100)->nullable();
            $table->string('unit', 50)->nullable();
            $table->string('source_type', 50)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'skipped', 'cancelled'])->default('pending');
            $table->text('observations')->nullable();
            $table->integer('sort_order')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });

        Schema::create('execution_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('execution_task_id')->constrained()->cascadeOnDelete();
            $table->dateTime('performed_at');
            $table->foreignId('performed_by')->constrained('users');
            $table->enum('status', ['completed', 'skipped', 'partially']);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('execution_logs');
        Schema::dropIfExists('execution_tasks');
        Schema::dropIfExists('execution_maps');
    }
};
