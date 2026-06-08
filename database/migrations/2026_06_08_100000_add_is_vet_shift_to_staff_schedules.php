<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsVetShiftToStaffSchedules extends Migration
{
    public function up()
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->boolean('is_vet_shift')->default(false)->after('notes');
        });
    }

    public function down()
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->dropColumn('is_vet_shift');
        });
    }
}
