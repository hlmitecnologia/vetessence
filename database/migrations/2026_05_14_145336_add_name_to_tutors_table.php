<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameToTutorsTable extends Migration
{
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->string('name')->nullable()->after('id');
        });
    }

    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
}
