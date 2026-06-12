<?php

namespace Tests\Feature\Controllers;

use Tests\ModuleTestCase;

class DocControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('docs.index'));

        $response->assertOk();
    }

    public function test_show_section()
    {
        $response = $this->get(route('docs.show', ['section' => 'user-manual']));

        $response->assertOk();
    }

    public function test_show_nested_page()
    {
        $response = $this->get(route('docs.page', ['section' => 'user-manual', 'page' => 'getting-started']));

        $response->assertOk();
    }

    public function test_show_returns_warning_for_missing_doc()
    {
        $response = $this->get(route('docs.show', ['section' => 'nonexistent-section']));

        $response->assertOk();
        $response->assertSee('Documento nao encontrado');
    }

    public function test_show_section_with_slash_page()
    {
        $response = $this->get(route('docs.show', ['section' => 'changelog']));

        $response->assertOk();
    }
}
