<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
            $table->string('zipcode', 20)->nullable()->after('city_id');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->foreignId('city_id')->nullable()->constrained('cities')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tutors', function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('city_id');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('city_id');
            $table->dropColumn('zipcode');
        });

        Schema::table('branches', function (Blueprint $table) {
            $table->dropConstrainedForeignId('state_id');
            $table->dropConstrainedForeignId('city_id');
        });
    }
};
