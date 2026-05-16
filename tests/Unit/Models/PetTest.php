<?php

namespace Tests\Unit\Models;

use App\Models\Pet;
use App\Models\Tutor;
use App\Models\Branch;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PetTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $branch = Branch::factory()->create();
        Pet::factory()->create([
            'name' => 'Rex', 'species' => 'canina', 'breed' => 'Labrador',
            'gender' => 'male', 'birth_date' => now()->subYears(2),
            'weight' => 25.50, 'color' => 'Dourado', 'is_active' => true,
            'created_at_branch_id' => $branch->id,
            'microchip_number' => '1234-5678-9012-3456',
            'microchip_date' => now()->subYear(),
            'rg_number' => 'RG-12345',
            'rg_issuer' => 'CFMV',
            'coat' => 'short',
            'size' => 'medium',
        ]);
        $this->assertDatabaseHas('pets', ['name' => 'Rex', 'microchip_number' => '1234-5678-9012-3456']);
    }

    public function test_microchip_casts()
    {
        $pet = Pet::factory()->create(['microchip_date' => '2024-01-15']);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $pet->microchip_date);
    }

    public function test_tutors_relationship()
    {
        $pet = Pet::factory()->create();
        $tutor = Tutor::factory()->create();
        $pet->tutors()->attach($tutor->id, ['is_primary' => true, 'relationship' => 'tutor']);
        $this->assertCount(1, $pet->tutors);
    }

    public function test_age_accessor()
    {
        $pet = Pet::factory()->create(['birth_date' => now()->subYears(2)]);
        $this->assertStringContainsString('ano', $pet->age);
    }

    public function test_age_months_accessor()
    {
        $pet = Pet::factory()->create(['birth_date' => now()->subMonths(3)]);
        $this->assertEquals(3, $pet->age_months);
    }

    public function test_null_birth_date()
    {
        $pet = Pet::factory()->create(['birth_date' => null]);
        $this->assertNull($pet->age);
    }

    public function test_created_at_branch_relationship()
    {
        $branch = Branch::factory()->create();
        $pet = Pet::factory()->create(['created_at_branch_id' => $branch->id]);
        $this->assertInstanceOf(Branch::class, $pet->createdAtBranch);
    }
}
