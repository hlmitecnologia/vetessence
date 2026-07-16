<?php

namespace Tests\Unit\Models;

use App\Models\Convenio;
use App\Models\ConvenioCoverageRule;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConvenioCoverageRuleTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);

        ConvenioCoverageRule::create([
            'convenio_id' => $convenio->id,
            'item_type' => 'service',
            'coverage_percent' => 80.00,
            'requires_pre_authorization' => true,
        ]);

        $this->assertDatabaseHas('convenio_coverage_rules', [
            'convenio_id' => $convenio->id,
            'item_type' => 'service',
            'coverage_percent' => 80.00,
        ]);
    }

    public function test_convenio_relationship()
    {
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $rule = ConvenioCoverageRule::create([
            'convenio_id' => $convenio->id,
            'item_type' => 'product',
            'coverage_percent' => 50,
        ]);

        $this->assertInstanceOf(Convenio::class, $rule->convenio);
    }

    public function test_coverage_percent_default()
    {
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $rule = ConvenioCoverageRule::create([
            'convenio_id' => $convenio->id,
            'item_type' => 'procedure',
            'coverage_percent' => 100,
        ]);

        $this->assertEquals(100, $rule->coverage_percent);
    }
}
