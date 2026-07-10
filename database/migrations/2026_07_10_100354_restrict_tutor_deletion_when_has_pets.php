<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop existing FK and recreate with RESTRICT instead of CASCADE
        // This prevents deleting a tutor that still has associated pets
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
        });
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->foreign('tutor_id')->references('id')->on('tutors')->restrictOnDelete();
        });

        // Also protect the pet side: prevent deleting a pet linked to tutors
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->dropForeign(['pet_id']);
        });
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->foreign('pet_id')->references('id')->on('pets')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->dropForeign(['tutor_id']);
        });
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->foreign('tutor_id')->references('id')->on('tutors')->cascadeOnDelete();
        });

        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->dropForeign(['pet_id']);
        });
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->foreign('pet_id')->references('id')->on('pets')->cascadeOnDelete();
        });
    }
};
