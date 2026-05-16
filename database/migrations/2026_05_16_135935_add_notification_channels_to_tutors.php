<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNotificationChannelsToTutors extends Migration
{
    public function up()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->boolean('notify_sms')->default(true);
            $table->boolean('notify_whatsapp')->default(true);
            $table->boolean('notify_email')->default(true);
        });
    }

    public function down()
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropColumn(['notify_sms', 'notify_whatsapp', 'notify_email']);
        });
    }
}
