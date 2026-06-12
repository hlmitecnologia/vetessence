<?php

namespace Tests\Unit\Commands;

use Illuminate\Support\Facades\File;
use Tests\ModuleTestCase;

class DocsPublishTest extends ModuleTestCase
{
    protected string $source;
    protected string $dest;

    protected function setUp(): void
    {
        parent::setUp();
        $this->source = resource_path('docs');
        $this->dest = storage_path('docs');
    }

    public function test_publishes_documentation_files(): void
    {
        File::ensureDirectoryExists($this->dest);

        $this->artisan('docs:publish')
            ->assertExitCode(0);

        $this->assertFileExists($this->dest . '/index.md');
        $this->assertDirectoryExists($this->dest . '/diagrams');
    }

    public function test_returns_error_when_source_directory_missing(): void
    {
        $renamed = resource_path('docs_backup');
        File::moveDirectory($this->source, $renamed);

        try {
            $this->artisan('docs:publish')
                ->expectsOutput('Source directory not found: ' . $this->source)
                ->assertExitCode(1);
        } finally {
            File::moveDirectory($renamed, $this->source);
        }
    }
}
