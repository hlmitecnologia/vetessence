<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use Tests\ModuleTestCase;

class EmployeeControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        User::factory()->count(3)->create();
        $response = $this->get(route('employees.index'));
        $response->assertOk();
    }

    public function test_index_filters_by_search()
    {
        User::factory()->create(['name' => 'João Silva']);
        User::factory()->create(['name' => 'Maria Souza']);
        $response = $this->get(route('employees.index', ['search' => 'João']));
        $response->assertOk();
    }

    public function test_show()
    {
        $user = User::factory()->create();
        $response = $this->get(route('employees.show', $user));
        $response->assertOk();
    }

    public function test_show_with_employee_parameter()
    {
        $user = User::factory()->create();
        $response = $this->get(route('employees.show', ['employee' => $user->id]));
        $response->assertOk();
    }
}
