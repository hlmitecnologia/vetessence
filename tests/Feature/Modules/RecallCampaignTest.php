<?php

namespace Tests\Feature\Modules;

use Illuminate\Support\Facades\Artisan;
use Tests\ModuleTestCase;

class RecallCampaignTest extends ModuleTestCase
{
    public function test_command_runs()
    {
        $exitCode = Artisan::call('recall:process');
        $this->assertEquals(0, $exitCode);
    }
}
