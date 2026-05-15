<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPixFieldsToInvoices extends Migration
{
    public function up()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->text('pix_code')->nullable()->after('payment_method');
            $table->timestamp('pix_expiration')->nullable()->after('pix_code');
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn(['pix_code', 'pix_expiration']);
        });
    }
}
