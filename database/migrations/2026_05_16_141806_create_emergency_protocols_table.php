<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmergencyProtocolsTable extends Migration
{
    public function up()
    {
        Schema::create('emergency_protocols', function (Blueprint $table) {
            $table->id();
            $table->string('title', 200);
            $table->string('slug', 200)->unique();
            $table->string('species', 50)->nullable();
            $table->enum('severity', ['critical', 'urgent', 'stable'])->default('urgent');
            $table->text('description')->nullable();
            $table->text('procedure_steps');
            $table->string('medications', 500)->nullable();
            $table->string('category', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('emergency_protocols');
    }
}
