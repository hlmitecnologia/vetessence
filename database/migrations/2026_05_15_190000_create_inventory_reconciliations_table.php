<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryReconciliationsTable extends Migration
{
    public function up()
    {
        Schema::create('inventory_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('expected_quantity', 10, 2);
            $table->decimal('actual_quantity', 10, 2);
            $table->decimal('variance', 10, 2);
            $table->string('type', 20)->default('manual');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('reconciled_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('inventory_reconciliations');
    }
}
