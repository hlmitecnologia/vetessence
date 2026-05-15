<?php

namespace Tests\Unit\Models;

use App\Models\Convenio;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConvenioTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Convenio::create([
            'name' => 'Health Plan',
            'cnpj' => '00.000.000/0001-00',
            'plan_name' => 'Gold',
            'discount_percent' => 10.00,
            'is_active' => true,
        ]);

        $this->assertDatabaseHas('convenios', [
            'name' => 'Health Plan',
            'cnpj' => '00.000.000/0001-00',
            'is_active' => true,
        ]);
    }

    public function test_convenioPets_relationship()
    {
        $convenio = Convenio::create([
            'name' => 'Health Plan',
            'cnpj' => '00.000.000/0001-00',
            'is_active' => true,
        ]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $convenio->convenioPets);
    }

    public function test_is_active_cast()
    {
        $convenio = Convenio::create([
            'name' => 'Health Plan',
            'cnpj' => '00.000.000/0001-00',
            'is_active' => true,
        ]);

        $this->assertIsBool($convenio->is_active);
    }
}
