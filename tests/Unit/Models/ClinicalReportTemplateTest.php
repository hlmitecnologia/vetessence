<?php

namespace Tests\Unit\Models;

use App\Models\ClinicalReportTemplate;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ClinicalReportTemplateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        ClinicalReportTemplate::create([
            'name' => 'Relatorio Clinico',
            'slug' => 'relatorio-clinico',
            'species' => 'canino',
            'specialty' => 'clinica-geral',
            'category' => 'consulta',
            'description' => 'Modelo de relatorio clinico',
            'content' => '<h1>Relatorio</h1>',
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('clinical_report_templates', [
            'name' => 'Relatorio Clinico',
            'slug' => 'relatorio-clinico',
            'species' => 'canino',
            'is_active' => true,
        ]);
    }

    public function test_active_scope()
    {
        ClinicalReportTemplate::create(['name' => 'A', 'slug' => 'a', 'is_active' => true]);
        ClinicalReportTemplate::create(['name' => 'B', 'slug' => 'b', 'is_active' => false]);

        $this->assertCount(1, ClinicalReportTemplate::active()->get());
    }

    public function test_for_species_scope()
    {
        ClinicalReportTemplate::create(['name' => 'Canino', 'slug' => 'canino', 'species' => 'canino']);
        ClinicalReportTemplate::create(['name' => 'Felino', 'slug' => 'felino', 'species' => 'felino']);
        ClinicalReportTemplate::create(['name' => 'Generico', 'slug' => 'generico', 'species' => null]);

        $this->assertCount(2, ClinicalReportTemplate::forSpecies('canino')->get());
    }
}
