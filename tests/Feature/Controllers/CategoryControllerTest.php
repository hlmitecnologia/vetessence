<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use Tests\ModuleTestCase;

class CategoryControllerTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('veterinario');
    }

    public function test_index()
    {
        Category::factory()->count(3)->create();
        $response = $this->get(route('categories.index'));
        $response->assertOk();
    }

    public function test_create()
    {
        $response = $this->get(route('categories.create'));
        $response->assertOk();
    }

    public function test_store_creates_record()
    {
        $response = $this->post(route('categories.store'), [
            'name' => 'Medicamentos',
            'type' => 'product',
        ]);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Medicamentos', 'type' => 'product']);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->post(route('categories.store'), ['name' => '']);
        $response->assertSessionHasErrors(['name', 'type']);
    }

    public function test_show()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('categories.show', $category));
        $response->assertOk();
    }

    public function test_edit()
    {
        $category = Category::factory()->create();
        $response = $this->get(route('categories.edit', $category));
        $response->assertOk();
    }

    public function test_update_modifies_record()
    {
        $category = Category::factory()->create(['name' => 'Antiga']);
        $response = $this->put(route('categories.update', $category), [
            'name' => 'Alimentos',
            'type' => 'product',
        ]);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Alimentos']);
    }

    public function test_destroy_deletes_record()
    {
        $category = Category::factory()->create();
        $response = $this->delete(route('categories.destroy', $category));
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_fails_with_children()
    {
        $parent = Category::factory()->create();
        Category::factory()->create(['parent_id' => $parent->id]);
        $response = $this->delete(route('categories.destroy', $parent));
        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $parent->id]);
    }

    public function test_store_with_parent()
    {
        $parent = Category::factory()->create(['type' => 'product']);
        $response = $this->post(route('categories.store'), [
            'name' => 'Subcategoria',
            'type' => 'product',
            'parent_id' => $parent->id,
        ]);
        $response->assertRedirect(route('categories.index'));
        $this->assertDatabaseHas('categories', ['name' => 'Subcategoria', 'parent_id' => $parent->id]);
    }
}
