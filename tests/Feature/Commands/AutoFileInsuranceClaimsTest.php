<?php

namespace Tests\Feature\Commands;

use App\Models\Convenio;
use App\Models\ConvenioClaim;
use App\Models\ConvenioPet;
use App\Models\Setting;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Tests\ModuleTestCase;

class AutoFileInsuranceClaimsTest extends ModuleTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Setting::set('porto_seguro_api_key', 'test-key');
        Setting::set('porto_seguro_api_secret', 'test-secret');
        Setting::set('porto_seguro_base_url', 'https://api.portoseguro.test/v1');
    }

    public function test_command_submits_pending_claims(): void
    {
        Http::fake([
            'api.portoseguro.test/v1/claims' => Http::response([
                'id' => 'PS-12345',
                'status' => 'received',
            ], 200),
        ]);

        $convenio = Convenio::factory()->create(['name' => 'Porto Seguro']);
        $convenioPet = ConvenioPet::factory()->create(['convenio_id' => $convenio->id]);
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
            'status' => 'pending',
        ]);

        $exitCode = Artisan::call('claims:auto-file');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode, "Command failed. Output: {$output}");

        $claim->refresh();
        $this->assertEquals('submitted', $claim->status, "Output: {$output}");
        $this->assertEquals('PS-12345', $claim->external_id);
    }

    public function test_command_skips_non_pending_claims(): void
    {
        $convenio = Convenio::factory()->create(['name' => 'Porto Seguro']);
        $convenioPet = ConvenioPet::factory()->create(['convenio_id' => $convenio->id]);
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
            'status' => 'submitted',
        ]);

        $exitCode = Artisan::call('claims:auto-file');
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode, "Command failed. Output: {$output}");

        $claim->refresh();
        $this->assertEquals('submitted', $claim->status);
    }

    public function test_command_dry_run_does_not_submit(): void
    {
        $convenio = Convenio::factory()->create(['name' => 'Porto Seguro']);
        $convenioPet = ConvenioPet::factory()->create(['convenio_id' => $convenio->id]);
        $claim = ConvenioClaim::factory()->create([
            'convenio_pet_id' => $convenioPet->id,
            'status' => 'pending',
        ]);

        $exitCode = Artisan::call('claims:auto-file', ['--dry-run' => true]);
        $output = Artisan::output();

        $this->assertEquals(0, $exitCode, "Command failed. Output: {$output}");

        $claim->refresh();
        $this->assertEquals('pending', $claim->status);
    }
}
