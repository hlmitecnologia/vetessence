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
        ]);
        $this->assertDatabaseHas('pets', ['name' => 'Rex', 'species' => 'canina']);
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
