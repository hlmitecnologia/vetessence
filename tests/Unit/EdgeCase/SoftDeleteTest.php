<?php

namespace Tests\Unit\EdgeCase;

use Tests\TestCase;

class SoftDeleteTest extends TestCase
{
    public function test_soft_delete_not_implemented()
    {
        $this->markTestSkipped('Nenhum modelo utiliza SoftDeletes no momento.');
    }
}
