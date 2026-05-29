<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationLogsStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->index(['status', 'sent_at']);
            $table->index('type');
            $table->index('channel');
        });
    }

    public function down()
    {
        Schema::table('notification_logs', function (Blueprint $table) {
            $table->dropIndex(['status', 'sent_at']);
            $table->dropIndex(['type']);
            $table->dropIndex(['channel']);
        });
    }
}
