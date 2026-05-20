<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfse_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->foreignId('invoice_id')->nullable()->constrained()->onDelete('set null');
            $table->string('nfse_number')->nullable();
            $table->string('nfse_code')->nullable();
            $table->string('nfse_url_xml')->nullable();
            $table->string('nfse_url_pdf')->nullable();
            $table->string('rps_number')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('issuance_date')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('verification_code')->nullable();
            $table->text('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('nfse_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfse_invoices');
    }
};
