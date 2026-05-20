<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'nfse_status')) {
                $table->string('nfse_status')->default('none')->after('status');
            }
            if (!Schema::hasColumn('invoices', 'nfse_invoice_id')) {
                $table->foreignId('nfse_invoice_id')->nullable()->after('nfse_status')
                    ->constrained('nfse_invoices')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('nfse_invoice_id');
            $table->dropColumn('nfse_status');
        });
    }
};
