<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEuthanasiaColumnsToPetDeathRecords extends Migration
{
    public function up()
    {
        Schema::table('pet_death_records', function (Blueprint $table) {
            $table->foreignId('authorized_by')->nullable()->after('registered_by')->constrained('users')->nullOnDelete();
            $table->string('authorization_doc', 100)->nullable()->after('authorized_by');
            $table->string('cremation_type', 50)->nullable()->after('disposition');
            $table->date('cremation_pickup_date')->nullable()->after('cremation_type');
            $table->text('cremation_notes')->nullable()->after('cremation_pickup_date');
            $table->text('memorial_text')->nullable()->after('cremation_notes');
        });
    }

    public function down()
    {
        Schema::table('pet_death_records', function (Blueprint $table) {
            $table->dropForeign(['authorized_by']);
            $table->dropColumn(['authorized_by', 'authorization_doc', 'cremation_type', 'cremation_pickup_date', 'cremation_notes', 'memorial_text']);
        });
    }
}
