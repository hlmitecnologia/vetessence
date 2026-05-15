<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceStaffSchedulesForOnCall extends Migration
{
    public function up()
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->index('user_id');
            $table->dropUnique(['user_id', 'work_date']);

            $table->boolean('is_on_call')->default(false)->after('shift_type');
            $table->string('on_call_type', 30)->nullable()->after('is_on_call');
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->after('on_call_type');
        });
    }

    public function down()
    {
        Schema::table('staff_schedules', function (Blueprint $table) {
            $table->dropColumn(['branch_id', 'on_call_type', 'is_on_call']);
            $table->unique(['user_id', 'work_date']);
        });
    }
}
