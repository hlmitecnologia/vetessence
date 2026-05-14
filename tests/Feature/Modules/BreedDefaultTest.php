<?php

namespace Tests\Feature\Modules;

use App\Models\BreedDefault;
use Tests\ModuleTestCase;

class BreedDefaultTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        $response = $this->get(route('breed-defaults.index'));
        $response->assertOk();
    }

    public function test_unique_species_breed()
    {
        BreedDefault::factory()->create(['species' => 'canino', 'breed' => 'labrador']);

        try {
            BreedDefault::factory()->create(['species' => 'canino', 'breed' => 'labrador']);
        } catch (\Illuminate\Database\QueryException $e) {
            // Unique constraint violation expected
        }

        $this->assertEquals(1, BreedDefault::where('species', 'canino')->where('breed', 'labrador')->count());
    }
}
