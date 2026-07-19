<?php

namespace Tests\Feature\Controllers\Api;

use App\Models\Invoice;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Tests\ModuleTestCase;

class InvoiceControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_returns_paginated_invoices()
    {
        Invoice::factory()->create();
        Invoice::factory()->create();

        $response = $this->getJson('/api/v1/invoices');

        $response->assertOk();
        $response->assertJsonStructure(['data', 'current_page', 'total']);
    }

    public function test_index_filters_by_status()
    {
        $paid = Invoice::factory()->create(['status' => 'paid']);
        Invoice::factory()->create(['status' => 'pending']);

        $response = $this->getJson('/api/v1/invoices?status=paid');

        $response->assertOk();
        $response->assertJsonFragment(['id' => $paid->id]);
    }

    public function test_show_returns_invoice()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/v1/invoices/' . $invoice->id);

        $response->assertOk();
        $response->assertJsonPath('id', $invoice->id);
    }

    public function test_show_returns_404_for_nonexistent_invoice()
    {
        $response = $this->getJson('/api/v1/invoices/99999');

        $response->assertNotFound();
        $response->assertJson(['error' => 'Fatura não encontrada']);
    }

    public function test_show_includes_relations()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $user = User::factory()->create();
        $invoice = Invoice::factory()->create([
            'tutor_id' => $tutor->id,
            'pet_id' => $pet->id,
            'user_id' => $user->id,
        ]);

        $response = $this->getJson('/api/v1/invoices/' . $invoice->id);

        $response->assertOk();
        $response->assertJsonStructure(['tutor', 'pet', 'items', 'creator']);
    }

    public function test_my_invoices_returns_404_when_user_has_no_tutor_relationship()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->getJson('/api/v1/my-invoices');

        $response->assertNotFound();
        $response->assertJson(['error' => 'Tutor não encontrado']);
    }

    public function test_index_returns_without_auth()
    {
        Invoice::factory()->create();

        $response = $this->getJson('/api/v1/invoices');

        $response->assertOk();
    }
}
