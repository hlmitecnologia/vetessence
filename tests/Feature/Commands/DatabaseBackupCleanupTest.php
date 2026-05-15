<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DatabaseBackupCleanupTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_cleans_old_backups()
    {
        $this->artisan('backup:cleanup')
            ->assertExitCode(0);
    }

    public function test_command_respects_keep_option()
    {
        $this->artisan('backup:cleanup', ['--keep' => 7])
            ->assertExitCode(0);
    }
}
