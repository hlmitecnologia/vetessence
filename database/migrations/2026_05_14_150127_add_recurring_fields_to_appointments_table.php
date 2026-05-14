<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecurringFieldsToAppointmentsTable extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->boolean('is_recurring')->default(false)->after('notes');
            $table->string('recurrence_rule', 100)->nullable()->after('is_recurring');
            $table->date('recurrence_end_date')->nullable()->after('recurrence_rule');
            $table->unsignedBigInteger('parent_appointment_id')->nullable()->after('recurrence_end_date');

            $table->foreign('parent_appointment_id')->references('id')->on('appointments')->cascadeOnDelete();
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['parent_appointment_id']);
            $table->dropColumn(['is_recurring', 'recurrence_rule', 'recurrence_end_date', 'parent_appointment_id']);
        });
    }
}
