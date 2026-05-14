<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHealthCertificatesTable extends Migration
{
    public function up()
    {
        Schema::create('health_certificates', function (Blueprint $table) {
            $table->id();
            $table->string('certificate_number', 50)->unique();
            $table->foreignId('pet_id')->constrained()->cascadeOnDelete();
            $table->string('type', 50); // international, domestic, boarding, exhibition, other
            $table->string('destination', 255)->nullable();
            $table->foreignId('issuer_vet_id')->constrained('users');
            $table->date('issue_date');
            $table->date('expiration_date')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->boolean('is_export')->default(false);
            $table->string('status', 20)->default('draft'); // draft, issued, expired, cancelled
            $table->timestamp('pdf_generated_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('health_certificates');
    }
}
