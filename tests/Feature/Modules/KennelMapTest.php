<?php

namespace Tests\Feature\Modules;

use Tests\ModuleTestCase;

class KennelMapTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_kennel_map()
    {
        $response = $this->get(route('boardings.kennel-map'));
        $response->assertOk();
    }
}
