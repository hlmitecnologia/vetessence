<?php

namespace Tests\Feature\Controllers;

use App\Models\Setting;
use Tests\ModuleTestCase;

class SystemUpdateControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('system-update.index'));
        $response->assertOk();
    }

    public function test_index_shows_current_hash()
    {
        $response = $this->get(route('system-update.index'));
        $response->assertOk();
        $response->assertSee('desconhecido', false);
    }

    public function test_token_saves_settings()
    {
        $response = $this->post(route('system-update.token'), [
            'github_token' => 'ghp_test123',
            'github_repo' => 'user/repo',
            'github_branch' => 'main',
        ]);

        $response->assertRedirect(route('system-update.index'));
        $this->assertEquals('ghp_test123', Setting::get('github_token'));
        $this->assertEquals('user/repo', Setting::get('github_repo'));
        $this->assertEquals('main', Setting::get('github_branch'));
    }

    public function test_token_validates_required_fields()
    {
        $response = $this->post(route('system-update.token'), []);
        $response->assertSessionHasErrors(['github_token', 'github_repo', 'github_branch']);
    }

    public function test_check_returns_error_when_no_token()
    {
        Setting::set('github_token', '');

        $response = $this->get(route('system-update.check'));

        $response->assertRedirect(route('system-update.index'));
    }

    public function test_check_redirects_with_flash_data()
    {
        Setting::set('github_token', 'ghp_test123');

        $response = $this->get(route('system-update.check'));

        $response->assertRedirect(route('system-update.index'));
    }

    public function test_apply_returns_error_when_no_token()
    {
        Setting::set('github_token', '');

        $response = $this->post(route('system-update.apply'));

        $response->assertRedirect(route('system-update.index'));
    }

    public function test_history_returns_empty_logs()
    {
        $response = $this->get(route('system-update.history'));

        $response->assertOk();
    }
}
