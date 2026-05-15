<?php

namespace Tests\Unit\Models;

use App\Models\ControlledSubstance;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ControlledSubstanceTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        ControlledSubstance::create([
            'name' => 'Cetamina', 'active_ingredient' => 'Cetamina', 'schedule' => 'C1',
            'unit' => 'ml', 'current_stock' => 100.00, 'min_stock' => 10.00, 'is_active' => true,
        ]);
        $this->assertDatabaseHas('controlled_substances', ['name' => 'Cetamina', 'current_stock' => 100.00]);
    }

    public function test_is_active_cast()
    {
        $s = ControlledSubstance::create(['name' => 'Teste', 'schedule' => 'C1', 'unit' => 'ml', 'is_active' => true]);
        $this->assertIsBool($s->is_active);
    }
}
