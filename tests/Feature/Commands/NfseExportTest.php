<?php

namespace Tests\Feature\Commands;

use App\Models\Branch;
use App\Models\NfseInvoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NfseExportTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_exports_issued_nfse()
    {
        NfseInvoice::factory()->create([
            'status' => 'issued',
            'nfse_url_xml' => 'https://example.com/test.xml',
        ]);

        $this->artisan('nfse:export', ['--from' => now()->subDay()->format('Y-m-d'), '--to' => now()->addDay()->format('Y-m-d')])
            ->assertExitCode(0);
    }

    public function test_command_returns_success_with_no_results()
    {
        $this->artisan('nfse:export', ['--from' => '2020-01-01', '--to' => '2020-01-31'])
            ->assertExitCode(0);
    }

    public function test_command_filters_by_branch()
    {
        $branch = Branch::factory()->create();
        NfseInvoice::factory()->create([
            'branch_id' => $branch->id,
            'status' => 'issued',
            'nfse_url_xml' => 'https://example.com/test.xml',
        ]);

        $this->artisan('nfse:export', [
            '--from' => now()->subDay()->format('Y-m-d'),
            '--to' => now()->addDay()->format('Y-m-d'),
            '--branch' => $branch->id,
        ])->assertExitCode(0);
    }
}
