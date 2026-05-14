<?php

namespace Tests\Feature\Modules;

use Tests\ModuleTestCase;

class BackupTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('backups.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('backups.create'));
        $response->assertRedirect();
    }
}
