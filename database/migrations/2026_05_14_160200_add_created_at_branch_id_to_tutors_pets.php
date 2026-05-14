<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCreatedAtBranchIdToTutorsPets extends Migration
{
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->foreignId('created_at_branch_id')->nullable()->constrained('branches')->nullOnDelete()->after('notes');
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->foreignId('created_at_branch_id')->nullable()->constrained('branches')->nullOnDelete()->after('notes');
        });
    }

    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropForeign(['created_at_branch_id']);
            $table->dropColumn('created_at_branch_id');
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['created_at_branch_id']);
            $table->dropColumn('created_at_branch_id');
        });
    }
}
