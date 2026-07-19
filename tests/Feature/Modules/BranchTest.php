<?php

namespace Tests\Feature\Modules;

use App\Models\Branch;
use App\Models\User;
use Tests\ModuleTestCase;

class BranchTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('branches.index'));
        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('branches.store'), [
            'name' => 'Unidade Centro',
            'city' => 'São Paulo',
            'state' => 'SP',
            'phone' => '1133333333',
            'cnpj' => '12345678000199',
            'ie' => '123456789',
            'im' => '987654321',
            'is_main' => true,
            'is_active' => true,
        ]);
        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseHas('branches', ['name' => 'Unidade Centro', 'is_main' => true]);
    }

    public function test_only_one_main()
    {
        Branch::factory()->create(['is_main' => true]);
        $this->post(route('branches.store'), [
            'name' => 'Unidade Nova', 'is_main' => true, 'is_active' => true,
            'cnpj' => '98765432000199', 'ie' => '987654321', 'im' => '123456789',
        ]);
        $this->assertEquals(1, Branch::where('is_main', true)->count());
    }

    public function test_destroy_blocked_with_users()
    {
        $branch = Branch::factory()->create();
        $user = User::factory()->create(['branch_id' => $branch->id]);
        $response = $this->delete(route('branches.destroy', $branch));
        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id]);
    }

    public function test_destroy_allowed_without_users()
    {
        $branch = Branch::factory()->create();
        $this->delete(route('branches.destroy', $branch));
        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }
}
