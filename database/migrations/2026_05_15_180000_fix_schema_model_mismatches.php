<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixSchemaModelMismatches extends Migration
{
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->integer('duration')->nullable()->after('time');
            $table->string('room', 50)->nullable()->after('duration');
            $table->foreignId('created_by')->nullable()->after('room')->constrained('users')->nullOnDelete();
        });

        Schema::table('appointment_services', function (Blueprint $table) {
            $table->decimal('discount', 10, 2)->default(0)->after('price');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
        });

        Schema::table('convenios', function (Blueprint $table) {
            $table->text('coverage')->nullable()->after('is_active');
            $table->integer('max_consults_month')->nullable()->after('coverage');
            $table->string('contract_number', 50)->nullable()->after('max_consults_month');
            $table->date('start_date')->nullable()->after('contract_number');
            $table->date('end_date')->nullable()->after('start_date');
        });

        Schema::table('medical_records', function (Blueprint $table) {
            $table->foreignId('vet_id')->nullable()->after('user_id')->constrained('users')->nullOnDelete();
            $table->text('chief_complaint')->nullable()->after('type');
            $table->time('time')->nullable()->after('date');
            $table->json('vital_signs')->nullable()->after('chief_complaint');
            $table->json('attachments')->nullable()->after('diagnosis');
            $table->text('anamnesis')->nullable()->after('chief_complaint');
            $table->text('physical_exam')->nullable()->after('anamnesis');
            $table->text('prognosis')->nullable()->after('treatment');
            $table->foreignId('record_id')->nullable()->after('id')->constrained('medical_records')->nullOnDelete();
            $table->integer('version')->default(1)->after('record_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->string('unit', 30)->nullable()->after('sku');
            $table->string('barcode', 50)->nullable()->after('unit');
            $table->decimal('max_stock', 10, 2)->nullable()->after('stock');
        });

        Schema::table('surgeries', function (Blueprint $table) {
            $table->string('anesthesia_type', 50)->nullable()->after('surgery_type');
            $table->integer('surgery_duration')->nullable()->after('cost');
            $table->foreignId('medical_record_id')->nullable()->after('pet_id')->constrained()->nullOnDelete();
        });

        Schema::table('vaccinations', function (Blueprint $table) {
            $table->string('manufacturer', 100)->nullable()->after('vaccine');
            $table->string('application_site', 100)->nullable()->after('manufacturer');
            $table->string('lot', 50)->nullable()->after('batch');
            $table->string('dose', 50)->nullable()->after('lot');
            $table->foreignId('medical_record_id')->nullable()->after('pet_id')->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropColumn(['duration', 'room', 'created_by']);
        });
        Schema::table('appointment_services', function (Blueprint $table) {
            $table->dropColumn('discount');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('description');
        });
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropColumn(['coverage', 'max_consults_month', 'contract_number', 'start_date', 'end_date']);
        });
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropForeign(['vet_id']);
            $table->dropForeign(['record_id']);
            $table->dropColumn(['vet_id', 'chief_complaint', 'time', 'vital_signs', 'attachments', 'anamnesis', 'physical_exam', 'prognosis', 'record_id', 'version']);
        });
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['unit', 'barcode', 'max_stock']);
        });
        Schema::table('surgeries', function (Blueprint $table) {
            $table->dropForeign(['medical_record_id']);
            $table->dropColumn(['anesthesia_type', 'surgery_duration', 'medical_record_id']);
        });
        Schema::table('vaccinations', function (Blueprint $table) {
            $table->dropForeign(['medical_record_id']);
            $table->dropColumn(['manufacturer', 'application_site', 'lot', 'dose', 'medical_record_id']);
        });
    }
}
