<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class MinimalTest extends DuskTestCase
{
    public function test_browser_launches(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->waitForText('Entrar')
                ->assertSee('Entrar')
                ->assertSee('Lembrar-me')
                ->assertInputValue('input[name="email"]', '')
                ->type('email', 'test@test.com')
                ->assertInputValue('input[name="email"]', 'test@test.com')
                ->screenshot('login-page');
        });
    }
}
