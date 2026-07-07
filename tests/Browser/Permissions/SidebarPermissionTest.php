<?php

namespace Tests\Browser\Permissions;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class SidebarPermissionTest extends DuskTestCase
{
    use TestsFlows;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();
    }

    public function test_tutor_cannot_access_restricted_routes(): void
    {
        $user = $this->createUser('tutor');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/users')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard')
                ->assertSee('Acesso negado');
        });
    }

    public function test_receptionist_cannot_access_configuracoes(): void
    {
        $user = $this->createUser('recepcionista');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/configuracoes/branding')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard')
                ->assertSee('Acesso negado');
        });
    }

    public function test_admin_can_access_restricted_routes(): void
    {
        $user = $this->createUser('admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/users')
                ->waitForText('Usuários')
                ->assertPathIs('/users')
                ->assertSee('Usuários');
        });
    }

    public function test_veterinario_cannot_access_configuracoes(): void
    {
        $user = $this->createUser('veterinario');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/configuracoes/branding')
                ->waitForLocation('/dashboard')
                ->assertPathIs('/dashboard')
                ->assertSee('Acesso negado');
        });
    }
}
