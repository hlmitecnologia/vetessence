<?php

namespace Tests\Unit\Models;

use App\Models\ParasiteControl;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ParasiteControlTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $pet = Pet::factory()->create();
        ParasiteControl::create([
            'pet_id' => $pet->id, 'product_name' => 'Advantage',
            'active_ingredient' => 'Imidacloprido', 'type' => 'topico',
            'application_date' => now(),
        ]);
        $this->assertDatabaseHas('parasite_controls', ['pet_id' => $pet->id, 'product_name' => 'Advantage']);
    }

    public function test_pet_relationship()
    {
        $pet = Pet::factory()->create();
        $pc = ParasiteControl::create(['pet_id' => $pet->id, 'product_name' => 'Teste', 'type' => 'topico', 'application_date' => now()]);
        $this->assertInstanceOf(Pet::class, $pc->pet);
    }

    public function test_vet_relationship()
    {
        $pet = Pet::factory()->create();
        $vet = User::factory()->create();
        $pc = ParasiteControl::create(['pet_id' => $pet->id, 'vet_id' => $vet->id, 'product_name' => 'Teste', 'type' => 'topico', 'application_date' => now()]);
        $this->assertInstanceOf(User::class, $pc->vet);
    }

    public function test_overdue_scope()
    {
        $pet = Pet::factory()->create();
        ParasiteControl::create(['pet_id' => $pet->id, 'product_name' => 'Vencido', 'type' => 'topico', 'application_date' => now()->subMonths(3), 'next_due_date' => now()->subMonth()]);
        $this->assertCount(1, ParasiteControl::overdue()->get());
    }
}
