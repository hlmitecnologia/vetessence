<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLabEquipmentIntegrationsTable extends Migration
{
    public function up()
    {
        Schema::create('lab_equipment_integrations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('equipment_type', 100); // hematology, biochemistry, urinalysis, etc.
            $table->string('protocol', 50)->default('rest'); // rest, hl7, fhir, custom
            $table->string('endpoint_url', 500)->nullable();
            $table->string('api_key', 255)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->integer('port')->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('last_contact_at')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_equipment_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_id')->constrained('lab_equipment_integrations');
            $table->string('result_identifier', 255)->nullable();
            $table->foreignId('pet_id')->nullable()->constrained();
            $table->foreignId('laboratory_order_id')->nullable()->constrained();
            $table->string('test_type', 100);
            $table->json('raw_data');
            $table->json('parsed_results')->nullable();
            $table->string('status', 20)->default('received'); // received, processed, error
            $table->text('error_message')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lab_equipment_results');
        Schema::dropIfExists('lab_equipment_integrations');
    }
}
