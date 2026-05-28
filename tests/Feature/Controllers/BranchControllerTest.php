<?php

namespace Tests\Feature\Controllers;

use App\Models\Branch;
use App\Models\User;
use Tests\ModuleTestCase;

class BranchControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Branch::factory()->count(3)->create();
        $response = $this->get(route('branches.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('branches.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('branches.store'), [
            'name' => 'Nova Unidade',
            'city' => 'São Paulo',
            'state' => 'SP',
        ]);
        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseHas('branches', ['name' => 'Nova Unidade']);
    }

    public function test_store_validates_name()
    {
        $response = $this->post(route('branches.store'), ['name' => '']);
        $response->assertSessionHasErrors('name');
    }

    public function test_show()
    {
        $branch = Branch::factory()->create();
        $response = $this->get(route('branches.show', $branch));
        $response->assertOk();
    }

    public function test_edit()
    {
        $branch = Branch::factory()->create();
        $response = $this->get(route('branches.edit', $branch));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $branch = Branch::factory()->create(['name' => 'Antigo Nome']);
        $response = $this->put(route('branches.update', $branch), [
            'name' => 'Novo Nome',
        ]);
        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseHas('branches', ['id' => $branch->id, 'name' => 'Novo Nome']);
    }

    public function test_destroy_deletes_record()
    {
        $branch = Branch::factory()->create();
        $response = $this->delete(route('branches.destroy', $branch));
        $response->assertRedirect(route('branches.index'));
        $this->assertDatabaseMissing('branches', ['id' => $branch->id]);
    }

    public function test_destroy_fails_with_users()
    {
        $branch = Branch::factory()->create();
        User::factory()->create(['branch_id' => $branch->id]);

        $response = $this->delete(route('branches.destroy', $branch));
        $response->assertRedirect();
        $this->assertDatabaseHas('branches', ['id' => $branch->id]);
    }
}
