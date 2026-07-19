<?php

namespace Tests\Unit\Models;

use App\Models\Branch;
use App\Models\City;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BranchTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        $slug = fake()->unique()->slug();
        Branch::create([
            'name' => 'Unidade Teste',
            'slug' => $slug,
            'address' => 'Rua A, 123',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'state_id' => null,
            'city_id' => null,
            'zip_code' => '01234567',
            'phone' => '11999999999',
            'email' => 'teste@vet.com',
            'cnpj' => '12345678000199',
            'is_active' => true,
            'is_main' => true,
            'notes' => 'Unidade principal',
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Unidade Teste',
            'slug' => $slug,
            'is_active' => true,
            'is_main' => true,
        ]);
    }

    public function test_active_scope()
    {
        $ids = [];
        $ids[] = Branch::create(['name' => 'A', 'slug' => fake()->unique()->slug(), 'is_active' => true])->id;
        $ids[] = Branch::create(['name' => 'B', 'slug' => fake()->unique()->slug(), 'is_active' => false])->id;
        $ids[] = Branch::create(['name' => 'C', 'slug' => fake()->unique()->slug(), 'is_active' => true])->id;

        $active = Branch::active()->whereIn('id', $ids)->get();
        $this->assertCount(2, $active);
    }

    public function test_main_scope()
    {
        $ids = [];
        $ids[] = Branch::create(['name' => 'A', 'slug' => fake()->unique()->slug(), 'is_main' => true])->id;
        $ids[] = Branch::create(['name' => 'B', 'slug' => fake()->unique()->slug(), 'is_main' => false])->id;
        $ids[] = Branch::create(['name' => 'C', 'slug' => fake()->unique()->slug(), 'is_main' => true])->id;

        $main = Branch::main()->whereIn('id', $ids)->get();
        $this->assertCount(2, $main);
    }

    public function test_users_relationship()
    {
        $branch = Branch::create(['name' => 'Filial', 'slug' => fake()->unique()->slug()]);
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $this->assertTrue($branch->users->contains($user));
    }

    public function test_state_relationship()
    {
        $state = State::factory()->create();
        $branch = Branch::create([
            'name' => 'Filial',
            'slug' => fake()->unique()->slug(),
            'state_id' => $state->id,
        ]);

        $this->assertTrue($branch->state->is($state));
    }

    public function test_city_relationship()
    {
        $state = State::factory()->create();
        $city = City::factory()->create(['state_id' => $state->id]);
        $branch = Branch::create([
            'name' => 'Filial',
            'slug' => fake()->unique()->slug(),
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertTrue($branch->city->is($city));
    }
}
