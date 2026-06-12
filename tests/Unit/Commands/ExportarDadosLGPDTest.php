<?php

namespace Tests\Unit\Commands;

use App\Models\Tutor;
use Illuminate\Support\Facades\Storage;
use Tests\ModuleTestCase;

class ExportarDadosLGPDTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake();
    }

    public function test_exports_tutor_data(): void
    {
        $tutor = Tutor::factory()->create([
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
        ]);

        $this->artisan('lgpd:export', ['tutor_id' => $tutor->id])
            ->assertExitCode(0);

        $files = Storage::files('exports');
        $this->assertCount(1, $files);
        $this->assertStringContainsString("lgpd-export-{$tutor->id}-", $files[0]);
    }

    public function test_returns_error_when_tutor_not_found(): void
    {
        $this->artisan('lgpd:export', ['tutor_id' => 9999])
            ->expectsOutput('Tutor não encontrado.')
            ->assertExitCode(1);
    }
}
