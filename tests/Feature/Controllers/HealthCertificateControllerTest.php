<?php

namespace Tests\Feature\Controllers;

use App\Models\HealthCertificate;
use App\Models\Pet;
use App\Models\User;
use Tests\ModuleTestCase;

class HealthCertificateControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        HealthCertificate::factory()->count(3)->create();
        $response = $this->get(route('health-certificates.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_status()
    {
        HealthCertificate::factory()->create(['status' => 'draft']);
        HealthCertificate::factory()->create(['status' => 'issued']);

        $response = $this->get(route('health-certificates.index', ['status' => 'issued']));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('health-certificates.create'));
        $response->assertOk();
    }

    public function test_store_creates_certificate()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();

        $response = $this->post(route('health-certificates.store'), [
            'pet_id' => $pet->id,
            'type' => 'international',
            'issuer_vet_id' => $vet->id,
            'issue_date' => now()->format('Y-m-d'),
            'expiration_date' => now()->addDays(30)->format('Y-m-d'),
            'status' => 'draft',
        ]);

        $response->assertRedirect(route('health-certificates.index'));
        $this->assertDatabaseHas('health_certificates', [
            'pet_id' => $pet->id,
            'type' => 'international',
        ]);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('health-certificates.store'), []);
        $response->assertSessionHasErrors(['pet_id', 'type', 'issuer_vet_id', 'issue_date', 'status']);
    }

    public function test_show()
    {
        $certificate = HealthCertificate::factory()->create();
        $response = $this->get(route('health-certificates.show', $certificate));
        $response->assertOk();
    }

    public function test_edit()
    {
        $certificate = HealthCertificate::factory()->create();
        $response = $this->get(route('health-certificates.edit', $certificate));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $certificate = HealthCertificate::factory()->create(['destination' => 'Brazil']);
        $newDest = 'Argentina';

        $response = $this->put(route('health-certificates.update', $certificate), [
            'pet_id' => $certificate->pet_id,
            'type' => $certificate->type,
            'issuer_vet_id' => $certificate->issuer_vet_id,
            'issue_date' => $certificate->issue_date->format('Y-m-d'),
            'expiration_date' => $certificate->expiration_date->format('Y-m-d'),
            'status' => $certificate->status,
            'destination' => $newDest,
        ]);

        $response->assertRedirect(route('health-certificates.index'));
        $this->assertDatabaseHas('health_certificates', [
            'id' => $certificate->id,
            'destination' => $newDest,
        ]);
    }

    public function test_destroy_deletes_record()
    {
        $certificate = HealthCertificate::factory()->create();

        $response = $this->delete(route('health-certificates.destroy', $certificate));

        $response->assertRedirect(route('health-certificates.index'));
        $this->assertDatabaseMissing('health_certificates', ['id' => $certificate->id]);
    }

    public function test_pdf_generates_and_updates_status()
    {
        $certificate = HealthCertificate::factory()->create(['status' => 'draft']);

        $response = $this->get(route('health-certificates.pdf', $certificate));

        $response->assertOk();
        $this->assertDatabaseHas('health_certificates', [
            'id' => $certificate->id,
            'status' => 'issued',
        ]);
        $this->assertNotNull($certificate->fresh()->pdf_generated_at);
    }

    public function test_download_cvi_pdf()
    {
        $certificate = HealthCertificate::factory()->create(['status' => 'draft']);

        $response = $this->get(route('health-certificates.cvi-pdf', $certificate));

        $response->assertOk();
        $this->assertDatabaseHas('health_certificates', [
            'id' => $certificate->id,
            'status' => 'issued',
        ]);
    }
}
