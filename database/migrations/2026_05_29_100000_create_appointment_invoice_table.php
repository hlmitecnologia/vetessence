<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('appointment_invoice', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['appointment_id', 'invoice_id']);
        });

        if (Schema::hasColumn('invoices', 'appointment_id')) {
            DB::statement('INSERT INTO appointment_invoice (appointment_id, invoice_id, created_at, updated_at)
                SELECT appointment_id, id, NOW(), NOW() FROM invoices WHERE appointment_id IS NOT NULL');

            Schema::table('invoices', function (Blueprint $table) {
                $table->dropConstrainedForeignId('appointment_id');
            });
        }
    }

    public function down()
    {
        if (!Schema::hasColumn('invoices', 'appointment_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('appointment_id')->nullable()->constrained()->nullOnDelete();
            });

            DB::statement('UPDATE invoices i
                JOIN appointment_invoice ai ON ai.invoice_id = i.id
                SET i.appointment_id = ai.appointment_id
                WHERE i.appointment_id IS NULL');
        }

        Schema::dropIfExists('appointment_invoice');
    }
};
