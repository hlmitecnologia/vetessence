<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCviFieldsToHealthCertificates extends Migration
{
    public function up()
    {
        Schema::table('health_certificates', function (Blueprint $table) {
            $table->string('cvi_number', 50)->nullable()->unique()->after('id');
            $table->string('destination_country', 100)->nullable()->after('expiration_date');
            $table->string('transport_mode', 50)->nullable()->after('destination_country');
            $table->date('embarkation_date')->nullable()->after('transport_mode');
            $table->string('crmv_emitter', 30)->nullable()->after('embarkation_date');
            $table->json('requirements_checklist')->nullable()->after('crmv_emitter');
            $table->boolean('is_cvi')->default(false)->after('requirements_checklist');
            $table->foreignId('approved_by')->nullable()->after('is_cvi')->constrained('users')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('health_certificates', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['cvi_number', 'destination_country', 'transport_mode', 'embarkation_date', 'crmv_emitter', 'requirements_checklist', 'is_cvi', 'approved_by']);
        });
    }
}
