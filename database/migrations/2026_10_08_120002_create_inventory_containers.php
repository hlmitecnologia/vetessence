<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_containers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_batch_id')->constrained()->cascadeOnDelete();
            $table->string('identifier', 100);
            $table->decimal('initial_quantity', 14, 4);
            $table->decimal('available_quantity', 14, 4);
            $table->string('unit', 30);
            $table->string('status', 30)->default('unopened');
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('reconstituted_at')->nullable();
            $table->timestamp('beyond_use_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['inventory_batch_id', 'identifier']);
            $table->index(['status', 'beyond_use_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_containers');
    }
};
