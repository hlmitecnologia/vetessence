<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->foreignId('nfe_invoice_id')->nullable()->after('nfse_invoice_id')->constrained('nfe_invoices');
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['nfe_invoice_id']);
            $table->dropColumn('nfe_invoice_id');
        });
    }
};
