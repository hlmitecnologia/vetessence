<?php

namespace Tests\Feature\Modules;

use App\Models\Pet;
use App\Models\Tutor;
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
}
