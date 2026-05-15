<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInsuranceFieldsToConvenios extends Migration
{
    public function up()
    {
        Schema::table('convenios', function (Blueprint $table) {
            $table->boolean('pre_authorization_required')->default(false)->after('is_active');
            $table->json('coverage_details')->nullable()->after('pre_authorization_required');
            $table->string('claim_form_url', 255)->nullable()->after('coverage_details');
        });

        Schema::create('convenio_claims', function (Blueprint $table) {
            $table->id();
            $table->foreignId('convenio_pet_id')->constrained('convenio_pet')->cascadeOnDelete();
            $table->foreignId('invoice_id')->nullable()->constrained()->nullOnDelete();
            $table->string('claim_number', 50)->unique();
            $table->string('status', 30)->default('draft');
            $table->decimal('amount_requested', 10, 2);
            $table->decimal('amount_approved', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('filed_at')->nullable();
            $table->timestamp('response_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('convenio_claims');
        Schema::table('convenios', function (Blueprint $table) {
            $table->dropColumn(['pre_authorization_required', 'coverage_details', 'claim_form_url']);
        });
    }
}
