<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentGatewaysTable extends Migration
{
    public function up()
    {
        Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('provider', 50); // mercadopago, pagseguro, stripe, pix, etc.
            $table->boolean('is_active')->default(false);
            $table->boolean('is_sandbox')->default(true);
            $table->text('public_key')->nullable();
            $table->text('secret_key')->nullable();
            $table->text('webhook_secret')->nullable();
            $table->string('webhook_url', 500)->nullable();
            $table->json('config')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'gateway_id')) {
                $table->foreignId('gateway_id')->nullable()->constrained('payment_gateways');
                $table->string('gateway_transaction_id', 255)->nullable();
                $table->string('gateway_status', 50)->nullable();
                $table->timestamp('gateway_paid_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['gateway_id']);
            $table->dropColumn(['gateway_id', 'gateway_transaction_id', 'gateway_status', 'gateway_paid_at']);
        });
        Schema::dropIfExists('payment_gateways');
    }
}
