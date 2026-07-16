<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('boarding_id')->nullable()->after('medical_record_id')
                ->constrained('boardings')->nullOnDelete();
            $table->foreignId('convenio_subscription_id')->nullable()->after('boarding_id')
                ->constrained('convenio_subscriptions')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['boarding_id']);
            $table->dropForeign(['convenio_subscription_id']);
            $table->dropColumn(['boarding_id', 'convenio_subscription_id']);
        });
    }
};
