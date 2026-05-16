<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugFormularyTable extends Migration
{
    public function up()
    {
        Schema::create('drug_formulary', function (Blueprint $table) {
            $table->id();
            $table->string('drug', 150);
            $table->string('species', 50);
            $table->decimal('dosage_mg_kg', 10, 2);
            $table->decimal('max_dose', 10, 2)->nullable();
            $table->string('route', 50)->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drug_formulary');
    }
}
