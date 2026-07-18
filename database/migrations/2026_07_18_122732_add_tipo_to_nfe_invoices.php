<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nfe_invoices', function (Blueprint $table) {
            $table->string('tipo', 10)->default('nfce')->after('branch_id');
        });
    }

    public function down(): void
    {
        Schema::table('nfe_invoices', function (Blueprint $table) {
            $table->dropColumn('tipo');
        });
    }
};
