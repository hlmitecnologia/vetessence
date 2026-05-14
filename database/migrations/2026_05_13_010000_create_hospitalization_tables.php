<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // ===== 1. HOSPITALIZATION =====
        Schema::create('hospitalizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('admission_date');
            $table->time('admission_time')->nullable();
            $table->text('admission_reason');
            $table->text('initial_diagnosis')->nullable();
            $table->string('department', 50)->nullable();
            $table->string('bed', 50)->nullable();
            $table->boolean('is_emergency')->default(false);
            $table->string('status', 30)->default('admitted'); // admitted, transferred, discharged
            $table->date('discharged_at')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->text('discharge_instructions')->nullable();
            $table->timestamps();
        });

        Schema::create('hospitalization_daily_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->date('record_date');
            $table->string('shift', 20); // morning, afternoon, night
            $table->text('subjective')->nullable();
            $table->text('objective')->nullable();
            $table->text('assessment')->nullable();
            $table->text('plan')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->string('appetite', 20)->nullable(); // normal, reduced, absent
            $table->string('hydration', 20)->nullable(); // normal, dehydrated, overhydrated
            $table->string('urination', 30)->nullable();
            $table->string('defecation', 30)->nullable();
            $table->text('medications_given')->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('hospitalization_fluid_therapy', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained()->cascadeOnDelete();
            $table->string('fluid_type', 100);
            $table->string('rate', 50)->nullable();
            $table->decimal('volume', 8, 2)->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->string('route', 30)->default('iv'); // iv, sc
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('hospitalization_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hospitalization_id')->constrained()->cascadeOnDelete();
            $table->string('medication');
            $table->string('dosage', 100)->nullable();
            $table->string('unit', 30)->nullable();
            $table->string('frequency', 100)->nullable();
            $table->string('route', 50)->default('oral');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('status', 20)->default('active'); // active, completed, suspended
            $table->foreignId('prescribed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== 2. WEIGHT TRACKING =====
        Schema::create('weight_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->decimal('weight', 6, 2);
            $table->integer('bcs')->nullable()->comment('Body Condition Score 1-9');
            $table->date('measurement_date');
            $table->foreignId('measured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== 3. VACCINATION REMINDERS =====
        Schema::create('vaccination_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vaccination_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->dateTime('sent_at')->nullable();
            $table->string('channel', 20)->nullable(); // email, sms, whatsapp
            $table->string('status', 20)->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 50); // vaccination_reminder, appointment_reminder, birthday, recall
            $table->string('channel', 20)->nullable();
            $table->string('destination', 200)->nullable();
            $table->dateTime('sent_at');
            $table->string('status', 20); // sent, failed
            $table->text('message')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // ===== 4. ANESTHESIA MONITORING =====
        Schema::create('anesthesia_monitorings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('surgery_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('anesthetist', 100)->nullable();
            $table->text('anesthetic_protocol')->nullable();
            $table->text('premedication')->nullable();
            $table->string('induction_agent', 100)->nullable();
            $table->string('maintenance_agent', 100)->nullable();
            $table->string('iv_access', 50)->nullable();
            $table->string('intubation_type', 50)->nullable();
            $table->dateTime('monitoring_start')->nullable();
            $table->dateTime('monitoring_end')->nullable();
            $table->string('fluid_type', 100)->nullable();
            $table->string('fluid_rate', 50)->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('anesthesia_vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anesthesia_monitoring_id')->constrained()->cascadeOnDelete();
            $table->dateTime('recorded_at');
            $table->integer('heart_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->integer('spo2')->nullable();
            $table->integer('etco2')->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('blood_pressure_mean')->nullable();
            $table->decimal('temperature', 4, 1)->nullable();
            $table->string('anesthetic_depth', 30)->nullable(); // light, surgical, deep
            $table->string('vaporizer_setting', 30)->nullable();
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        // ===== 5. TREATMENT PLANS =====
        Schema::create('treatment_plans', function (Blueprint $table) {
            $table->id();
            $table->string('plan_number', 30)->unique();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_estimated', 10, 2)->default(0);
            $table->decimal('total_authorized', 10, 2)->default(0);
            $table->string('status', 30)->default('draft'); // draft, pending, client_approved, authorized, in_progress, completed, cancelled
            $table->dateTime('client_approved_at')->nullable();
            $table->text('client_notes')->nullable();
            $table->text('vet_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('treatment_plan_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_plan_id')->constrained()->cascadeOnDelete();
            $table->string('description');
            $table->string('category', 30); // procedure, medication, exam, hospitalization, other
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->boolean('is_authorized')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== 6. DIGITAL CONSENT FORMS =====
        Schema::create('consent_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('content');
            $table->string('category', 30); // surgery, anesthesia, euthanasia, hospitalization, procedure
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('consent_forms', function (Blueprint $table) {
            $table->id();
            $table->string('consent_number', 30)->unique();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('consent_template_id')->nullable()->constrained()->nullOnDelete();
            $table->text('signed_content')->nullable();
            $table->string('client_name');
            $table->string('client_document', 20)->nullable();
            $table->foreignId('veterinarian_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('witness_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('signed_at')->nullable();
            $table->text('signature_data')->nullable();
            $table->string('status', 20)->default('pending'); // pending, signed, expired
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== 7. DENTAL CHARTING =====
        Schema::create('dental_charts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->date('examination_date');
            $table->string('procedure_type', 50)->default('consultation'); // consultation, cleaning, extraction, surgery
            $table->string('tartar_index', 20)->nullable(); // grade_0, grade_1, grade_2, grade_3
            $table->string('gingivitis_index', 20)->nullable();
            $table->string('halitosis', 30)->nullable(); // none, mild, moderate, severe
            $table->text('general_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('dental_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dental_chart_id')->constrained()->cascadeOnDelete();
            $table->string('tooth_number', 10); // 101-411 for dog, 101-311 for cat
            $table->string('quadrant', 10); // upper_right, upper_left, lower_left, lower_right
            $table->string('condition', 50); // normal, plaque, tartar, gingivitis, recession, fracture, mobility, abscess, missing, extracted
            $table->string('severity', 20)->nullable(); // mild, moderate, severe
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== 8. CONTROLLED SUBSTANCE LOG =====
        Schema::create('controlled_substances', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('active_ingredient', 100)->nullable();
            $table->string('schedule', 5); // A1, A2, A3, B1, C1 (ANVISA)
            $table->string('anvisa_register', 30)->nullable();
            $table->string('unit', 30);
            $table->decimal('current_stock', 10, 2)->default(0);
            $table->decimal('min_stock', 10, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('controlled_substance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('controlled_substance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 10); // in, out
            $table->decimal('quantity', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->text('reason');
            $table->foreignId('prescription_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('witness_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // ===== 9. CLIENT COMMUNICATION =====
        Schema::create('communication_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type', 50); // appointment_reminder, vaccination_reminder, birthday, recall, custom
            $table->string('channel', 30); // email, sms, whatsapp
            $table->string('subject', 200)->nullable();
            $table->text('content');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('communication_queue', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tutor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('pet_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('template_id')->nullable()->constrained('communication_templates')->nullOnDelete();
            $table->string('channel', 30);
            $table->string('destination', 200)->nullable();
            $table->text('message_content')->nullable();
            $table->dateTime('scheduled_at');
            $table->dateTime('sent_at')->nullable();
            $table->string('status', 20)->default('pending'); // pending, sent, failed
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        // ===== 10. LABORATORY & IMAGING =====
        Schema::create('laboratory_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('lab_name', 100)->nullable();
            $table->date('order_date');
            $table->date('result_date')->nullable();
            $table->string('status', 30)->default('requested'); // requested, collected, processing, ready, delivered, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('laboratory_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laboratory_order_id')->constrained()->cascadeOnDelete();
            $table->string('test_name');
            $table->string('test_code', 30)->nullable();
            $table->text('result')->nullable();
            $table->string('reference_range', 100)->nullable();
            $table->string('unit', 30)->nullable();
            $table->boolean('is_abnormal')->default(false);
            $table->text('observations')->nullable();
            $table->timestamps();
        });

        Schema::create('imaging_exams', function (Blueprint $table) {
            $table->id();
            $table->string('exam_number', 30)->unique();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('exam_type', 30); // xray, ultrasound, ct, mri, endoscopy
            $table->string('region', 100)->nullable();
            $table->text('findings')->nullable();
            $table->text('impression')->nullable();
            $table->text('recommendations')->nullable();
            $table->json('images')->nullable();
            $table->string('status', 30)->default('requested'); // requested, scheduled, completed, reviewed
            $table->foreignId('radiologist_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('exam_date')->nullable();
            $table->timestamps();
        });

        // ===== 11. REFERRAL MANAGEMENT =====
        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->string('referral_number', 30)->unique();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referring_vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('referring_clinic', 100)->nullable();
            $table->foreignId('receiving_vet_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('receiving_clinic', 100)->nullable();
            $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            $table->text('reason');
            $table->text('clinical_history')->nullable();
            $table->text('requested_procedures')->nullable();
            $table->text('attachments')->nullable();
            $table->string('status', 30)->default('sent'); // sent, accepted, completed, rejected
            $table->text('response_notes')->nullable();
            $table->date('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        $tables = [
            'referrals',
            'imaging_exams',
            'laboratory_tests', 'laboratory_orders',
            'communication_queue', 'communication_templates',
            'controlled_substance_logs', 'controlled_substances',
            'dental_conditions', 'dental_charts',
            'consent_forms', 'consent_templates',
            'treatment_plan_items', 'treatment_plans',
            'anesthesia_vital_signs', 'anesthesia_monitorings',
            'notification_logs', 'vaccination_reminders',
            'weight_records',
            'hospitalization_prescriptions', 'hospitalization_fluid_therapy',
            'hospitalization_daily_records', 'hospitalizations',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
