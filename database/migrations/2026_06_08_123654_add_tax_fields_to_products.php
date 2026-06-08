<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('ncm', 8)->nullable()->after('sale_price');
            $table->string('cest', 7)->nullable()->after('ncm');
            $table->string('cfop', 4)->nullable()->after('cest');
            $table->string('cst', 3)->nullable()->after('cfop');
            $table->string('csosn', 3)->nullable()->after('cst');
            $table->decimal('ibpt_percentage', 5, 2)->nullable()->after('csosn');
            $table->decimal('weight_kg', 8, 3)->nullable()->after('ibpt_percentage');
            $table->tinyInteger('icms_origin')->default(0)->after('weight_kg');
            $table->string('icms_cst', 3)->nullable()->after('icms_origin');
            $table->tinyInteger('icms_modbc')->nullable()->after('icms_cst');
            $table->decimal('icms_vbc', 12, 2)->nullable()->after('icms_modbc');
            $table->decimal('icms_picms', 5, 2)->nullable()->after('icms_vbc');
            $table->decimal('icms_predbc', 5, 2)->nullable()->after('icms_picms');
            $table->string('ipi_cst', 2)->nullable()->after('icms_predbc');
            $table->decimal('ipi_aliquot', 5, 2)->nullable()->after('ipi_cst');
            $table->string('pis_cst', 2)->nullable()->after('ipi_aliquot');
            $table->string('cofins_cst', 2)->nullable()->after('pis_cst');
            $table->decimal('pis_aliquot', 5, 2)->nullable()->after('cofins_cst');
            $table->decimal('cofins_aliquot', 5, 2)->nullable()->after('pis_aliquot');
            $table->string('fiscal_classification')->nullable()->after('cofins_aliquot');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'ncm', 'cest', 'cfop', 'cst', 'csosn', 'ibpt_percentage',
                'weight_kg', 'icms_origin', 'icms_cst', 'icms_modbc',
                'icms_vbc', 'icms_picms', 'icms_predbc',
                'ipi_cst', 'ipi_aliquot',
                'pis_cst', 'cofins_cst', 'pis_aliquot', 'cofins_aliquot',
                'fiscal_classification',
            ]);
        });
    }
};
