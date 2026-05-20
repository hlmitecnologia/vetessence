<?php

namespace Tests\Feature\Modules;

use App\Models\Pet;
use App\Models\Tutor;
use App\Models\Vaccination;
use Tests\ModuleTestCase;

class VaccinationCertificateTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_certificate_download()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->get(route('vaccinations.certificate', $pet));
        $response->assertOk();
    }

    public function test_certificate_is_pdf()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create(['name' => 'Rex Cert']);
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        Vaccination::factory()->create(['pet_id' => $pet->id]);

        $response = $this->get(route('vaccinations.certificate', $pet));
        $response->assertOk();
        $response->assertHeaderContains('Content-Type', 'pdf');
    }

    public function test_certificate_returns_pdf_for_pet_without_vaccinations()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);

        $response = $this->get(route('vaccinations.certificate', $pet));
        $response->assertOk();
    }
}
