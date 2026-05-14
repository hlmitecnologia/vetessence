<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBoardingTables extends Migration
{
    public function up()
    {
        Schema::create('boardings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 20)->default('boarding'); // boarding, grooming, both
            $table->dateTime('check_in_at');
            $table->dateTime('expected_check_out')->nullable();
            $table->dateTime('check_out_at')->nullable();
            $table->string('status', 20)->default('checked_in'); // checked_in, checked_out, cancelled
            $table->decimal('daily_rate', 10, 2)->default(0);
            $table->decimal('grooming_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->text('reason')->nullable();
            $table->text('feeding_instructions')->nullable();
            $table->text('medication_instructions')->nullable();
            $table->string('pickup_contact', 255)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('checked_out_by')->nullable()->constrained('users');
            $table->timestamps();
        });

        Schema::create('boarding_daily_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('boarding_id')->constrained()->cascadeOnDelete();
            $table->date('task_date');
            $table->string('task_name', 100);
            $table->text('description')->nullable();
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('completed_by')->nullable()->constrained('users');
            $table->text('observations')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('boarding_daily_tasks');
        Schema::dropIfExists('boardings');
    }
}
