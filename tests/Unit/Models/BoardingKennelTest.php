<?php

namespace Tests\Unit\Models;

use App\Models\BoardingKennel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BoardingKennelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        BoardingKennel::create(['name' => 'Kennel A', 'size' => 'small', 'capacity' => 1, 'is_active' => true]);
        $this->assertDatabaseHas('boarding_kennels', ['name' => 'Kennel A', 'is_active' => true]);
    }

    public function test_is_active_cast()
    {
        $kennel = BoardingKennel::create(['name' => 'Kennel B', 'size' => 'large', 'capacity' => 2, 'is_active' => true]);
        $this->assertIsBool($kennel->is_active);
    }
}
