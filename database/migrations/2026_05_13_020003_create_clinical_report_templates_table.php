<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClinicalReportTemplatesTable extends Migration
{
    public function up()
    {
        Schema::create('clinical_report_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('slug', 255)->unique();
            $table->string('species', 50)->nullable();
            $table->string('specialty', 100)->nullable();
            $table->string('category', 100)->nullable();
            $table->text('description')->nullable();
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('clinical_report_templates');
    }
}
