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
        VaccineProtocol::factory()->create(['species' => 'canine']);
        VaccineProtocol::factory()->create(['species' => 'feline']);

        $this->assertCount(1, VaccineProtocol::forSpecies('canine')->get());
    }

    public function test_core_scope()
    {
        VaccineProtocol::factory()->create(['is_core' => true]);
        VaccineProtocol::factory()->create(['is_core' => false]);

        $this->assertCount(1, VaccineProtocol::core()->get());
    }

    public function test_active_default()
    {
        $protocol = VaccineProtocol::factory()->create(['is_active' => true]);
        $this->assertTrue($protocol->is_active);
    }
}
