<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add is_active to pets (only if not exists)
        if (!Schema::hasColumn('pets', 'is_active')) {
            Schema::table('pets', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('photo_url');
            });
        }

        // Fix pet_tutor pivot - add relationship column (only if not exists)
        if (!Schema::hasColumn('pet_tutor', 'relationship')) {
            Schema::table('pet_tutor', function (Blueprint $table) {
                $table->string('relationship', 50)->nullable()->after('is_primary');
            });
        }

        // Create appointment_services pivot table
        if (!Schema::hasTable('appointment_services')) {
            Schema::create('appointment_services', function (Blueprint $table) {
                $table->id();
                $table->foreignId('appointment_id')->constrained()->cascadeOnDelete();
                $table->foreignId('service_id')->constrained()->cascadeOnDelete();
                $table->decimal('price', 10, 2)->nullable();
                $table->integer('quantity')->default(1);
                $table->timestamps();
            });
        }

        // Create Spatie permission tables (only if not exists)
        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('guard_name');
                $table->unsignedBigInteger('permission_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type', 'guard_name']);
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('guard_name');
                $table->unsignedBigInteger('role_id');
                $table->string('model_type');
                $table->unsignedBigInteger('model_id');
                $table->index(['model_id', 'model_type', 'guard_name']);
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('permission_id');
                $table->unsignedBigInteger('role_id');
                $table->foreign('permission_id')->references('id')->on('permissions')->onDelete('cascade');
                $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            });
        }

        // Add user_id to appointments for vet relationship (only if not exists)
        if (!Schema::hasColumn('appointments', 'user_id')) {
            Schema::table('appointments', function (Blueprint $table) {
                $table->foreignId('user_id')->nullable()->after('vet_id')->constrained()->nullOnDelete();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('role_has_permissions');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('model_has_roles');
        Schema::dropIfExists('model_has_permissions');
        Schema::dropIfExists('appointment_services');
        
        Schema::table('pet_tutor', function (Blueprint $table) {
            $table->dropColumn('relationship');
        });
    }
};
