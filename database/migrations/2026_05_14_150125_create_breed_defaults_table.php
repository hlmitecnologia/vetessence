<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBreedDefaultsTable extends Migration
{
    public function up()
    {
        Schema::create('breed_defaults', function (Blueprint $table) {
            $table->id();
            $table->string('species', 50);
            $table->string('breed', 100);
            $table->string('size', 20)->nullable();
            $table->decimal('avg_weight_min', 8, 2)->nullable();
            $table->decimal('avg_weight_max', 8, 2)->nullable();
            $table->integer('avg_lifespan_min')->nullable();
            $table->integer('avg_lifespan_max')->nullable();
            $table->string('temperament', 255)->nullable();
            $table->text('predispositions')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['species', 'breed']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('breed_defaults');
    }
}
