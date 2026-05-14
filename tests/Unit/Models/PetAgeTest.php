<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetAgeTest extends TestCase
{
    use DatabaseTransactions;

    public function test_age_attribute()
    {
        $pet = Pet::factory()->create([
            'birth_date' => now()->subYears(2),
        ]);

        $this->assertStringContainsString('ano', $pet->age);
    }

    public function test_age_months_attribute()
    {
        $pet = Pet::factory()->create([
            'birth_date' => now()->subMonths(3),
        ]);

        $this->assertEquals(3, $pet->age_months);
    }

    public function test_null_birth_date()
    {
        $pet = Pet::factory()->create([
            'birth_date' => null,
        ]);

        $this->assertNull($pet->age);
    }
}
