<?php

namespace Tests\Feature\Commands;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DatabaseBackupTest extends TestCase
{
    use DatabaseTransactions;

    public function test_command_attempts_database_backup()
    {
        $this->artisan('backup:database')
            ->assertExitCode(0);
    }

    public function test_command_with_compress_flag()
    {
        $this->artisan('backup:database', ['--compress' => true])
            ->assertExitCode(0);
    }
}
