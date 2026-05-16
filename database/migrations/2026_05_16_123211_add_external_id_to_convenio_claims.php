<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExternalIdToConvenioClaims extends Migration
{
    public function up()
    {
        if (Schema::hasTable('convenio_claims') && !Schema::hasColumn('convenio_claims', 'external_id')) {
            Schema::table('convenio_claims', function (Blueprint $table) {
                $table->string('external_id')->nullable()->after('claim_number');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('convenio_claims') && Schema::hasColumn('convenio_claims', 'external_id')) {
            Schema::table('convenio_claims', function (Blueprint $table) {
                $table->dropColumn('external_id');
            });
        }
    }
}
