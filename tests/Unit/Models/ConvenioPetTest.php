<?php

namespace Tests\Unit\Models;

use App\Models\ConvenioPet;
use App\Models\Convenio;
use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConvenioPetTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Saúde Pet', 'is_active' => true]);
        ConvenioPet::create(['pet_id' => $pet->id, 'convenio_id' => $convenio->id, 'start_date' => now()]);
        $this->assertDatabaseHas('convenio_pet', ['pet_id' => $pet->id, 'convenio_id' => $convenio->id]);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $cp = ConvenioPet::create(['pet_id' => $pet->id, 'convenio_id' => $convenio->id]);
        $this->assertInstanceOf(Pet::class, $cp->pet);
    }

    public function test_convenio_relationship()
    {
        $pet = Pet::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $cp = ConvenioPet::create(['pet_id' => $pet->id, 'convenio_id' => $convenio->id]);
        $this->assertInstanceOf(Convenio::class, $cp->convenio);
    }

    public function test_start_date_cast()
    {
        $pet = Pet::factory()->create();
        $convenio = Convenio::create(['name' => 'Plano Teste', 'is_active' => true]);
        $cp = ConvenioPet::create(['pet_id' => $pet->id, 'convenio_id' => $convenio->id, 'start_date' => '2026-01-15']);
        $this->assertInstanceOf(\Carbon\Carbon::class, $cp->start_date);
    }
}
