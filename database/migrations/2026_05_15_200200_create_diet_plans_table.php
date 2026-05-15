<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDietPlansTable extends Migration
{
    public function up()
    {
        Schema::create('diet_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medical_record_id')->nullable()->constrained()->nullOnDelete();
            $table->string('diet_type', 50);
            $table->string('brand', 100)->nullable();
            $table->string('product_name', 200)->nullable();
            $table->string('daily_amount', 100)->nullable();
            $table->integer('duration_days')->nullable();
            $table->text('instructions')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('diet_plans');
    }
}
