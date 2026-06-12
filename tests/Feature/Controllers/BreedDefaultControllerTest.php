<?php

namespace Tests\Feature\Controllers;

use App\Models\BreedDefault;
use Tests\ModuleTestCase;

class BreedDefaultControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_index()
    {
        BreedDefault::factory()->count(3)->create();

        $response = $this->get(route('breed-defaults.index'));

        $response->assertOk();
    }

    public function test_store()
    {
        $response = $this->post(route('breed-defaults.store'), [
            'species' => 'canino',
            'breed' => 'Labrador',
            'size' => 'grande',
            'avg_weight_min' => 25,
            'avg_weight_max' => 36,
            'avg_lifespan_min' => 10,
            'avg_lifespan_max' => 14,
            'temperament' => 'Amigável',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('breed-defaults.index'));
        $this->assertDatabaseHas('breed_defaults', ['breed' => 'Labrador']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('breed-defaults.store'), []);

        $response->assertSessionHasErrors(['species', 'breed']);
    }

    public function test_show()
    {
        $default = BreedDefault::factory()->create();

        $response = $this->get(route('breed-defaults.show', $default));

        $response->assertOk();
    }

    public function test_update()
    {
        $default = BreedDefault::factory()->create();

        $response = $this->put(route('breed-defaults.update', $default), [
            'species' => 'felino',
            'breed' => 'Siamês',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('breed-defaults.index'));
        $this->assertDatabaseHas('breed_defaults', [
            'id' => $default->id,
            'breed' => 'Siamês',
        ]);
    }

    public function test_destroy()
    {
        $default = BreedDefault::factory()->create();

        $response = $this->delete(route('breed-defaults.destroy', $default));

        $response->assertRedirect(route('breed-defaults.index'));
        $this->assertDatabaseMissing('breed_defaults', ['id' => $default->id]);
    }
}
