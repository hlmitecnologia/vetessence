<?php

namespace Tests\Unit\Models;

use App\Models\BreedDefault;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BreedDefaultTest extends TestCase
{
    use DatabaseTransactions;

    public function test_unique_constraint()
    {
        BreedDefault::create([
            'species' => 'canino',
            'breed' => 'Labrador',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        BreedDefault::create([
            'species' => 'canino',
            'breed' => 'Labrador',
        ]);
    }
}
