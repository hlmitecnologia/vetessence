<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsentLogsTable extends Migration
{
    public function up()
    {
        Schema::create('consent_logs', function (Blueprint $table) {
            $table->id();
            $table->morphs('consentable');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50);
            $table->string('purpose', 100);
            $table->boolean('granted')->default(true);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('consented_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('consent_logs');
    }
}
