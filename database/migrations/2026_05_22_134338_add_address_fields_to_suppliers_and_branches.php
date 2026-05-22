<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('number', 20)->nullable()->after('address');
            $table->string('neighborhood', 100)->nullable()->after('number');
            $table->string('complement', 100)->nullable()->after('neighborhood');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->string('number', 20)->nullable()->after('address');
            $table->string('neighborhood', 100)->nullable()->after('number');
            $table->string('complement', 100)->nullable()->after('neighborhood');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['number', 'neighborhood', 'complement']);
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn(['number', 'neighborhood', 'complement']);
        });
    }
};
