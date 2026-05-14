<?php

namespace Tests\Feature\Modules;

use App\Models\Tutor;
use Tests\ModuleTestCase;

class PortalAuthTest extends ModuleTestCase
{
    public function test_login_page()
    {
        $response = $this->get(route('portal.login'));
        $response->assertOk();
    }

    public function test_register_creates_tutor()
    {
        $response = $this->post(route('portal.register.store'), [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'phone' => '11999999999',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertRedirect(route('portal.dashboard'));
        $this->assertDatabaseHas('tutors', [
            'email' => 'joao@example.com',
        ]);
    }
}
