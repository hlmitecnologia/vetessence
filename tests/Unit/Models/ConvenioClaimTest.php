<?php

namespace Tests\Unit\Models;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Invoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ConvenioClaimTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $cp = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::create([
            'convenio_pet_id' => $cp->id,
            'claim_number' => 'CLM-000001',
            'status' => 'filed',
            'amount_requested' => 1500.00,
            'filed_at' => now(),
        ]);

        $this->assertDatabaseHas('convenio_claims', [
            'claim_number' => 'CLM-000001',
            'amount_requested' => 1500.00,
        ]);
    }

    public function test_convenio_pet_relationship()
    {
        $cp = ConvenioPet::factory()->create();
        $claim = ConvenioClaim::factory()->create(['convenio_pet_id' => $cp->id]);
        $this->assertInstanceOf(ConvenioPet::class, $claim->convenioPet);
    }

    public function test_status_scope()
    {
        ConvenioClaim::factory()->create(['status' => 'draft', 'claim_number' => 'CLM-1001']);
        ConvenioClaim::factory()->create(['status' => 'filed', 'claim_number' => 'CLM-1002']);
        $this->assertEquals(1, ConvenioClaim::where('status', 'draft')->count());
    }
}
