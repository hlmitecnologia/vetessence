<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalFieldsToTreatmentPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('treatment_plans', function (Blueprint $table) {
            if (!Schema::hasColumn('treatment_plans', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable();
            }
            if (!Schema::hasColumn('treatment_plans', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('treatment_plans', function (Blueprint $table) {
            $table->dropColumn(['rejected_at', 'rejection_reason']);
        });
    }
}
