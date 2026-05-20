<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\NfseInvoice;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NfseWebhookControllerTest extends TestCase
{
    use DatabaseTransactions;

    public function test_webhook_updates_status()
    {
        $branch = Branch::factory()->create();
        $nfse = NfseInvoice::factory()->create([
            'branch_id' => $branch->id,
            'status' => 'pending',
            'nfse_number' => '999999',
            'issuance_date' => null,
        ]);

        $response = $this->postJson("/api/webhooks/nfse/{$branch->id}", [
            'nfse_number' => '999999',
            'status' => 'issued',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('nfse_invoices', [
            'id' => $nfse->id,
            'status' => 'issued',
        ]);
    }

    public function test_webhook_returns_ok_without_matching_invoice()
    {
        $branch = Branch::factory()->create();

        $response = $this->postJson("/api/webhooks/nfse/{$branch->id}", [
            'nfse_number' => '000000',
            'status' => 'issued',
        ]);

        $response->assertOk();
    }

    public function test_webhook_returns_ok_without_payload()
    {
        $branch = Branch::factory()->create();

        $response = $this->postJson("/api/webhooks/nfse/{$branch->id}", []);

        $response->assertOk();
    }

    public function test_webhook_updates_only_matching_branch()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        NfseInvoice::factory()->create([
            'branch_id' => $branch1->id,
            'status' => 'pending',
            'nfse_number' => '111111',
            'issuance_date' => null,
        ]);
        $nfse2 = NfseInvoice::factory()->create([
            'branch_id' => $branch2->id,
            'status' => 'pending',
            'nfse_number' => '222222',
            'issuance_date' => null,
        ]);

        $this->postJson("/api/webhooks/nfse/{$branch1->id}", [
            'nfse_number' => '111111',
            'status' => 'issued',
        ]);

        $this->assertEquals('pending', $nfse2->fresh()->status);
    }
}
