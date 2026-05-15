<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDigitalSignatureToPrescriptionsAndMedicalRecords extends Migration
{
    public function up()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->text('digital_signature')->nullable()->after('notes');
            $table->timestamp('signed_at')->nullable()->after('digital_signature');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('content_hash', 64)->nullable()->after('notes');
            $table->text('digital_signature')->nullable()->after('content_hash');
            $table->timestamp('signed_at')->nullable()->after('digital_signature');
        });
    }

    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['digital_signature', 'signed_at']);
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn(['content_hash', 'digital_signature', 'signed_at']);
        });
    }
}
