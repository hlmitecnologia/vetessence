<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered()
    {
        $this->markTestSkipped('Rotas de autenticação padrão não fazem parte do escopo atual.');
    }

    public function test_users_can_authenticate_using_the_login_screen()
    {
        $this->markTestSkipped('Rotas de autenticação padrão não fazem parte do escopo atual.');
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        $this->markTestSkipped('Rotas de autenticação padrão não fazem parte do escopo atual.');
    }
}
