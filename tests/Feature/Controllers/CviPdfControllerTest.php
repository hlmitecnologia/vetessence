<?php

namespace Tests\Feature\Controllers;

use App\Models\HealthCertificate;
use App\Models\Pet;
use App\Models\Tutor;
use Tests\ModuleTestCase;

class CviPdfControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_download_cvi_pdf_returns_pdf()
    {
        $this->loginAs('veterinario');

        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create([
            'name' => 'Rex',
            'species' => 'canine',
            'breed' => 'Golden Retriever',
            'gender' => 'male',
        ]);
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $vet = $this->makeUser('veterinario', ['crmv' => 'SP-12345']);

        $cert = HealthCertificate::factory()->create([
            'pet_id' => $pet->id,
            'issuer_vet_id' => $vet->id,
            'is_cvi' => true,
            'cvi_number' => 'CVI-0001/2026',
            'destination_country' => 'Estados Unidos',
            'status' => 'draft',
        ]);

        $response = $this->get(route('health-certificates.cvi-pdf', $cert));
        $response->assertOk();
        $this->assertStringContainsString('application/pdf', $response->headers->get('Content-Type') ?? '');
        $this->assertStringContainsString('.pdf', $response->headers->get('Content-Disposition') ?? '');
    }

    public function test_download_cvi_pdf_requires_authentication()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = $this->makeUser('veterinario');

        $cert = HealthCertificate::factory()->create([
            'pet_id' => $pet->id,
            'issuer_vet_id' => $vet->id,
            'is_cvi' => true,
        ]);

        $response = $this->get(route('health-certificates.cvi-pdf', $cert));
        $response->assertRedirect(route('login'));
    }

    public function test_cvi_pdf_contains_expected_fields()
    {
        $this->loginAs('veterinario');

        $tutor = Tutor::factory()->create([
            'phone' => '11999999999',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);
        $pet = Pet::factory()->create([
            'name' => 'Rex',
            'species' => 'canine',
            'breed' => 'Golden Retriever',
            'gender' => 'male',
            'coat' => 'medium',
            'size' => 'large',
            'microchip_number' => '1234-5678-9012-3456',
        ]);
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $vet = $this->makeUser('veterinario', [
            'name' => 'Dr. João Silva',
            'crmv' => 'SP-12345',
        ]);

        $cert = HealthCertificate::factory()->create([
            'pet_id' => $pet->id,
            'issuer_vet_id' => $vet->id,
            'is_cvi' => true,
            'cvi_number' => 'CVI-0001/2026',
            'destination_country' => 'Estados Unidos',
            'transport_mode' => 'aéreo',
            'status' => 'draft',
            'clinical_notes' => 'Animal apto para viagem',
        ]);

        $response = $this->get(route('health-certificates.cvi-pdf', $cert));
        $response->assertOk();

        $pdfContent = $response->getContent();
        $tmpPath = tempnam(sys_get_temp_dir(), 'cvi_test_');
        file_put_contents($tmpPath, $pdfContent);
        $text = shell_exec("pdftotext " . escapeshellarg($tmpPath) . " -") ?: '';
        unlink($tmpPath);

        $this->assertStringContainsString('CERTIFICADO VETERINÁRIO INTERNACIONAL', $text);
        $this->assertStringContainsString('CVI-0001/2026', $text);
        $this->assertStringContainsString('Rex', $text);
        $this->assertStringContainsString('Dr. João Silva', $text);
        $this->assertStringContainsString('SP-12345', $text);
        $this->assertStringContainsString('Estados Unidos', $text);
        $this->assertStringContainsString('CRMV', $text);
        $this->assertStringContainsString('Resolução CFMV', $text);
        $this->assertStringContainsString('Assinatura Digital', $text);
    }
}
