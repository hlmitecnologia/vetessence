<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBranchIdToOperationalTables extends Migration
{
    private array $tables = [
        'appointments', 'medical_records', 'vaccinations', 'exams', 'surgeries',
        'prescriptions', 'invoices', 'invoice_items', 'stock_movements',
        'suppliers', 'categories',
        'boardings', 'therapy_sessions', 'teleconsultations', 'referrals',
        'communication_queue', 'notification_logs', 'staff_notes',
        'online_bookings', 'payment_gateways', 'lab_equipment_integrations',
        'hospitalizations', 'anesthesia_monitorings',
        'laboratory_orders', 'imaging_exams',
        'controlled_substances', 'controlled_substance_logs',
        'treatment_plans', 'consent_forms', 'dental_charts', 'weight_records',
        'health_certificates', 'parasite_controls',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (!Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->foreignId('branch_id')->nullable()->constrained()->nullOnDelete()->after('id');
                });
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if (Schema::hasColumn($table, 'branch_id')) {
                Schema::table($table, function (Blueprint $t) use ($table) {
                    $t->dropForeign(['branch_id']);
                    $t->dropColumn('branch_id');
                });
            }
        }
    }
}
