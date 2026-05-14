<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDrugInteractionsTable extends Migration
{
    public function up()
    {
        Schema::create('drug_interactions', function (Blueprint $table) {
            $table->id();
            $table->string('drug_a', 255);
            $table->string('drug_b', 255);
            $table->string('severity', 20); // contraindicated, caution, minor
            $table->text('description');
            $table->string('mechanism', 255)->nullable();
            $table->text('management')->nullable();
            $table->string('source', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('drug_interactions');
    }
}
