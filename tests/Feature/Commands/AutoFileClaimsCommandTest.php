<?php

namespace Tests\Feature\Commands;

use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AutoFileClaimsCommandTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_finds_no_draft_claims()
    {
        ConvenioClaim::factory()->create(['status' => 'filed']);
        $this->artisan('claims:auto-file')
            ->expectsOutput('Nenhum sinistro pendente para envio.')
            ->assertExitCode(0);
    }

    public function test_dry_run_does_not_change_status()
    {
        $cp = ConvenioPet::factory()->create();
        ConvenioClaim::factory()->create([
            'convenio_pet_id' => $cp->id,
            'status' => 'draft',
        ]);
        $this->artisan('claims:auto-file --dry-run')
            ->assertExitCode(0);
        $this->assertEquals('draft', ConvenioClaim::first()->status);
    }

    public function test_command_processes_draft_claims()
    {
        $cp = ConvenioPet::factory()->create();
        ConvenioClaim::factory()->create([
            'convenio_pet_id' => $cp->id,
            'status' => 'draft',
        ]);
        $this->artisan('claims:auto-file')
            ->assertExitCode(1);
        $this->assertEquals('filed', ConvenioClaim::first()->status);
    }
}
