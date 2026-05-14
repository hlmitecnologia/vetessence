<?php

namespace Tests\Feature\Modules;

use App\Models\HealthCertificate;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class HealthCertificateTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        $response = $this->get(route('health-certificates.index'));
        $response->assertOk();
    }

    public function test_store_creates_certificate()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');

        $response = $this->post(route('health-certificates.store'), [
            'pet_id' => $pet->id,
            'type' => 'international',
            'destination' => 'EUA',
            'issuer_vet_id' => $vet->id,
            'issue_date' => now()->format('Y-m-d'),
            'expiration_date' => now()->addDays(30)->format('Y-m-d'),
            'clinical_notes' => 'Animal saudável',
            'is_export' => true,
            'status' => 'draft',
        ]);
        $response->assertRedirect(route('health-certificates.index'));
        $this->assertDatabaseHas('health_certificates', ['destination' => 'EUA']);
    }

    public function test_generate_number_is_unique()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');

        $n1 = HealthCertificate::generateNumber();
        HealthCertificate::factory()->create([
            'certificate_number' => $n1,
            'pet_id' => $pet->id,
            'issuer_vet_id' => $vet->id,
        ]);
        $n2 = HealthCertificate::generateNumber();
        $this->assertNotEquals($n1, $n2);
        $this->assertMatchesRegularExpression('/^HC-\d{4}\/\d{4}$/', $n1);
    }

    public function test_pdf_returns_download()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');
        $cert = HealthCertificate::factory()->create([
            'pet_id' => $pet->id, 'issuer_vet_id' => $vet->id,
        ]);
        $response = $this->get(route('health-certificates.pdf', $cert));
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
    }

    public function test_status_filter()
    {
        HealthCertificate::factory()->create(['status' => 'draft']);
        HealthCertificate::factory()->create(['status' => 'issued']);
        $response = $this->get(route('health-certificates.index', ['status' => 'draft']));
        $response->assertOk();
    }
}
