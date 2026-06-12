<?php

namespace Tests\Feature\Controllers;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\ModuleTestCase;

class BackupControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index_lists_backups()
    {
        Storage::fake('local');
        Storage::put('backups/test.sql', 'dump content');

        $response = $this->get(route('backups.index'));

        $response->assertOk();
        $response->assertSee('test.sql');
    }

    public function test_index_shows_empty_when_no_backups()
    {
        Storage::fake('local');

        $response = $this->get(route('backups.index'));

        $response->assertOk();
    }

    public function test_create_runs_backup_command()
    {
        Artisan::shouldReceive('call')
            ->once()
            ->with('backup:database');
        Artisan::shouldReceive('output')
            ->once()
            ->andReturn('Backup created successfully.');

        $response = $this->get(route('backups.create'));

        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('success');
    }

    public function test_download()
    {
        Storage::fake('local');
        Storage::put('backups/test.sql', 'dump content');

        $response = $this->get(route('backups.download', 'test.sql'));

        $response->assertOk();
        $response->assertDownload('test.sql');
    }

    public function test_download_returns_404_when_not_found()
    {
        Storage::fake('local');

        $response = $this->get(route('backups.download', 'nonexistent.sql'));

        $response->assertNotFound();
    }

    public function test_destroy()
    {
        Storage::fake('local');
        Storage::put('backups/test.sql', 'dump content');

        $response = $this->delete(route('backups.destroy', 'test.sql'));

        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('success');
        Storage::assertMissing('backups/test.sql');
    }

    public function test_destroy_handles_missing_file()
    {
        Storage::fake('local');

        $response = $this->delete(route('backups.destroy', 'ghost.sql'));

        $response->assertRedirect(route('backups.index'));
        $response->assertSessionHas('success');
    }
}
