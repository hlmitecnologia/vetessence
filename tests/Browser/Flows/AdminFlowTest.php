<?php

namespace Tests\Browser\Flows;

use App\Models\Branch;
use Laravel\Dusk\Browser;
use Tests\Browser\TestsFlows;
use Tests\DuskTestCase;

class AdminFlowTest extends DuskTestCase
{
    use TestsFlows;

    protected function setUp(): void
    {
        parent::setUp();
        $this->setUpDuskFlows();
    }

    public function test_auto_update_flow(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/system-update')
                ->waitForText('Atualização do Sistema')
                ->assertSee('Configuração do Git')
                ->assertSee('Licença')
                ->assertSee('Status')
                ->assertSee('Histórico de Atualizações')
                ->assertSee('Salvar')
                ->assertSee('Verificar');
        });
    }

    public function test_branding_page(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/configuracoes/branding')
                ->waitForText('Personalização')
                ->assertSee('Nome da Clínica')
                ->assertSee('Logotipo')
                ->assertSee('Favicon')
                ->assertSee('Cor Primária')
                ->assertSee('Cor de Fundo da Tela de Login')
                ->assertPresent('input[name="clinic_name"]')
                ->assertPresent('input[name="primary_color"]')
                ->assertPresent('input[name="sidebar_logo_width"]');
        });
    }

    public function test_branch_crud(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/branches')
                ->waitForText('Unidades')
                ->assertSee('Nova Unidade');

            // Create branch
            $branchName = 'Unidade Teste ' . time();
            $browser->clickLink('Nova Unidade')
                ->waitForText('Nova Unidade')
                ->type('name', $branchName)
                ->type('phone', '(11) 99999-8888')
                ->type('email', 'teste@clinica.com')
                ->type('address', 'Rua Teste, 123')
                ->type('zip_code', '01310-100')
                ->press('Salvar')
                ->waitForText('Unidades')
                ->assertSee($branchName);
        });
    }

    public function test_user_list(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/users')
                ->waitForText('Usuários')
                ->assertSee('Novo')
                ->assertSee($user->name)
                ->assertSee($user->email);
        });
    }

    public function test_role_list(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/roles')
                ->waitForText('Perfis de Acesso')
                ->assertSee('Perfis de Acesso')
                ->assertSee('Novo')
                ->assertPresent('table');
        });
    }

    public function test_audit_log_page(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/audit-logs')
                ->waitForText('Logs de Auditoria')
                ->assertSee('Filtrar')
                ->assertSee('Limpar')
                ->assertPresent('select[name="action"]')
                ->assertPresent('input[name="date_from"]')
                ->assertPresent('input[name="date_to"]');
        });
    }

    public function test_backup_flow(): void
    {
        $user = $this->createUser('super-admin');

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/backups')
                ->waitForText('Backups do Sistema')
                ->assertSee('Novo Backup');
        });
    }
}
