<?php

namespace Tests\Unit\Commands;

use Illuminate\Support\Facades\DB;
use Tests\ModuleTestCase;

class DbImportGeoTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function test_returns_failure_when_megapedigreedb_not_available(): void
    {
        DB::shouldReceive('connection')
            ->with('megapedigreedb')
            ->andReturnSelf();

        DB::shouldReceive('getDatabaseName')
            ->andReturn(false);

        $this->artisan('db:import-geo')
            ->expectsOutput('megapedigreedb connection not available.')
            ->assertExitCode(1);
    }
}
