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
        Schema::create('breeds', function (Blueprint $table) {
            $table->id();
            $table->string('species', 50);
            $table->string('name', 200);
            $table->timestamps();
        });

        Schema::table('pets', function (Blueprint $table) {
            $table->foreignId('breed_id')->nullable()->after('breed')->constrained('breeds')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pets', function (Blueprint $table) {
            $table->dropForeign(['breed_id']);
            $table->dropColumn('breed_id');
        });

        Schema::dropIfExists('breeds');
    }
};
