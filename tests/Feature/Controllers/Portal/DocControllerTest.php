<?php

namespace Tests\Feature\Controllers\Portal;

use App\Models\Setting;
use App\Models\Tutor;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DocControllerTest extends TestCase
{
    use DatabaseTransactions;

    private string $docsDir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->docsDir = storage_path('docs/tutor-manual');
        if (!is_dir($this->docsDir)) {
            mkdir($this->docsDir, 0755, true);
        }

        file_put_contents($this->docsDir . '/index.md', "# Manual do Tutor\n\nBem-vindo ao portal.");
        file_put_contents($this->docsDir . '/01-login.md', "# Login\n\nComo acessar o sistema.");
        file_put_contents($this->docsDir . '/clinica.md', "# Clínica VetEssence\n\nBem-vindo à VetEssence.");

        $this->tutor = Tutor::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $this->actingAs($this->tutor, 'tutor');
    }

    protected function tearDown(): void
    {
        if (is_dir($this->docsDir)) {
            array_map('unlink', glob($this->docsDir . '/*.md'));
            rmdir($this->docsDir);
        }

        parent::tearDown();
    }

    public function test_index_renders_docs_page()
    {
        $response = $this->get(route('portal.docs.index'));

        $response->assertOk();
        $response->assertViewHas('html');
        $response->assertViewHas('sidebar');
        $response->assertViewHas('currentPage', 'index');
    }

    public function test_show_renders_doc_page()
    {
        $response = $this->get(route('portal.docs.show', '01-login'));

        $response->assertOk();
        $response->assertViewHas('currentPage', '01-login');
    }

    public function test_show_falls_back_to_index_when_page_not_found()
    {
        $response = $this->get(route('portal.docs.show', 'nonexistent-page'));

        $response->assertOk();
        $response->assertViewHas('currentPage', 'index');
    }

    public function test_show_replaces_clinic_name_in_markdown()
    {
        Setting::set('branding.clinic_name', 'Minha Clínica');

        $response = $this->get(route('portal.docs.show', 'clinica'));

        $response->assertOk();
        $response->assertViewHas('currentPage', 'clinica');
        $html = $response->viewData('html');
        $this->assertStringContainsString('Minha Clínica', $html);
    }

    public function test_show_sanitizes_page_parameter()
    {
        $response = $this->get('/portal/docs/special@chars!');

        $response->assertOk();
        $response->assertViewHas('currentPage', 'index');
    }

    public function test_show_returns_404_when_index_does_not_exist()
    {
        $md = $this->docsDir . '/index.md';
        $backup = $md . '.bak';
        rename($md, $backup);

        try {
            $response = $this->get(route('portal.docs.show', 'missing-all'));
            $response->assertNotFound();
        } finally {
            rename($backup, $md);
        }
    }
}
