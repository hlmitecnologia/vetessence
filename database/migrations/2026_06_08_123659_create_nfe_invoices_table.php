<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfe_invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained();
            $table->foreignId('invoice_id')->constrained();
            $table->string('nfe_number')->nullable();
            $table->string('nfe_key', 44)->nullable();
            $table->string('status')->default('issuing');
            $table->timestamp('issuance_date')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('nfe_url_xml')->nullable();
            $table->text('nfe_url_pdf')->nullable();
            $table->text('danfe_url')->nullable();
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfe_invoices');
    }
};
