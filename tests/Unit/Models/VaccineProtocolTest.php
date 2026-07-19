<?php

namespace Tests\Unit\Models;

use App\Models\VaccineProtocol;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VaccineProtocolTest extends TestCase
{
    use DatabaseTransactions;

    public function test_for_species_scope()
    {
        $ids = [];
        $ids[] = VaccineProtocol::factory()->create(['species' => 'canine'])->id;
        $ids[] = VaccineProtocol::factory()->create(['species' => 'feline'])->id;

        $this->assertCount(1, VaccineProtocol::whereIn('id', $ids)->forSpecies('canine')->get());
    }

    public function test_core_scope()
    {
        $ids = [];
        $ids[] = VaccineProtocol::factory()->create(['is_core' => true])->id;
        $ids[] = VaccineProtocol::factory()->create(['is_core' => false])->id;

        $this->assertCount(1, VaccineProtocol::whereIn('id', $ids)->core()->get());
    }

    public function test_active_default()
    {
        $protocol = VaccineProtocol::factory()->create(['is_active' => true]);
        $this->assertTrue($protocol->is_active);
    }
}
