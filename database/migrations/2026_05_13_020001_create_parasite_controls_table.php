<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateParasiteControlsTable extends Migration
{
    public function up()
    {
        Schema::create('parasite_controls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('product_name', 200);
            $table->string('active_ingredient', 200)->nullable();
            $table->string('type', 50); // flea, tick, heartworm, intestinal, combination
            $table->date('application_date');
            $table->date('next_due_date')->nullable();
            $table->string('dose', 100)->nullable();
            $table->string('batch', 100)->nullable();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('parasite_controls');
    }
}
