<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReminderSentToVaccinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->boolean('reminder_sent')->default(false)->after('next_date');
        });
    }

    public function down()
    {
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropColumn('reminder_sent');
        });
    }
}
