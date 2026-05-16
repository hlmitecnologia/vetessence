<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationHashToPrescriptions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            if (!Schema::hasColumn('prescriptions', 'verification_hash')) {
                $table->string('verification_hash', 64)->nullable()->unique()->after('notes');
            }
            if (!Schema::hasColumn('prescriptions', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('verification_hash');
            }
        });
    }

    public function down()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn(['verification_hash', 'verified_at']);
        });
    }
}
