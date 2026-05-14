<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHrFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete()->after('role_id');
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete()->after('department_id');
            $table->date('hire_date')->nullable()->after('position_id');
            $table->string('contract_type', 30)->nullable()->after('hire_date');
            $table->string('crmv', 20)->nullable()->after('contract_type');
            $table->string('emergency_contact', 100)->nullable()->after('crmv');
            $table->string('emergency_phone', 20)->nullable()->after('emergency_contact');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['emergency_phone', 'emergency_contact', 'crmv', 'contract_type', 'hire_date', 'position_id', 'department_id']);
        });
    }
}
