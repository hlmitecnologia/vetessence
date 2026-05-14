<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGroomingTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('grooming_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('species', 50)->nullable();
            $table->string('breed', 100)->nullable();
            $table->string('size', 20)->nullable();
            $table->json('services')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->integer('estimated_minutes')->default(60);
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('grooming_templates');
    }
}
