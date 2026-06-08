<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hospitalization_fluid_therapy', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->after('observations');
        });

        Schema::table('hospitalization_prescriptions', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->after('notes');
        });

        Schema::table('treatment_plan_items', function (Blueprint $table) {
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('treatment_plan_items', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('hospitalization_prescriptions', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });

        Schema::table('hospitalization_fluid_therapy', function (Blueprint $table) {
            $table->dropForeign(['branch_id']);
            $table->dropColumn('branch_id');
        });
    }
};
