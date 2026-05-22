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
        Branch::create([
            'name' => 'Matriz',
            'slug' => 'matriz',
            'address' => 'Rua A, 123',
            'city' => 'Sao Paulo',
            'state' => 'SP',
            'state_id' => null,
            'city_id' => null,
            'zip_code' => '01234567',
            'phone' => '11999999999',
            'email' => 'matriz@vet.com',
            'cnpj' => '12345678000199',
            'is_active' => true,
            'is_main' => true,
            'notes' => 'Unidade principal',
        ]);

        $this->assertDatabaseHas('branches', [
            'name' => 'Matriz',
            'slug' => 'matriz',
            'is_active' => true,
            'is_main' => true,
        ]);
    }

    public function test_active_scope()
    {
        Branch::create(['name' => 'A', 'slug' => 'a', 'is_active' => true]);
        Branch::create(['name' => 'B', 'slug' => 'b', 'is_active' => false]);
        Branch::create(['name' => 'C', 'slug' => 'c', 'is_active' => true]);

        $active = Branch::active()->whereIn('slug', ['a', 'b', 'c'])->get();
        $this->assertCount(2, $active);
    }

    public function test_main_scope()
    {
        Branch::create(['name' => 'A', 'slug' => 'a', 'is_main' => true]);
        Branch::create(['name' => 'B', 'slug' => 'b', 'is_main' => false]);
        Branch::create(['name' => 'C', 'slug' => 'c', 'is_main' => true]);

        $main = Branch::main()->whereIn('slug', ['a', 'b', 'c'])->get();
        $this->assertCount(2, $main);
    }

    public function test_users_relationship()
    {
        $branch = Branch::create(['name' => 'Filial', 'slug' => 'filial']);
        $user = User::factory()->create(['branch_id' => $branch->id]);

        $this->assertTrue($branch->users->contains($user));
    }

    public function test_state_relationship()
    {
        $state = State::factory()->create();
        $branch = Branch::create([
            'name' => 'Filial',
            'slug' => 'filial',
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
            'slug' => 'filial',
            'state_id' => $state->id,
            'city_id' => $city->id,
        ]);

        $this->assertTrue($branch->city->is($city));
    }
}
