<?php

namespace Tests\Unit\Models;

use App\Models\HealthCertificate;
use App\Models\Pet;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class HealthCertificateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_number_format()
    {
        $number = HealthCertificate::generateNumber();
        $this->assertMatchesRegularExpression('/^HC-\d{4}\/\d{4}$/', $number);
    }

    public function test_pet_relationship()
    {
        $tutor = Tutor::factory()->create();
        $pet = Pet::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true]);
        $vet = User::factory()->create();

        $cert = HealthCertificate::factory()->create([
            'pet_id' => $pet->id,
            'issuer_vet_id' => $vet->id,
        ]);

        $this->assertInstanceOf(Pet::class, $cert->pet);
        $this->assertEquals($pet->id, $cert->pet->id);
    }
}
