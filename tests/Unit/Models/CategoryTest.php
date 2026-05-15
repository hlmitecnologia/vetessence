<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseTransactions;

    public function test_fillable()
    {
        Category::create(['type' => 'product', 'name' => 'Medicamentos']);
        $this->assertDatabaseHas('categories', ['name' => 'Medicamentos']);
    }

    public function test_parent_relationship()
    {
        $parent = Category::create(['type' => 'product', 'name' => 'Medicamentos']);
        $child = Category::create(['type' => 'product', 'name' => 'Antibióticos', 'parent_id' => $parent->id]);
        $this->assertInstanceOf(Category::class, $child->parent);
        $this->assertEquals($parent->id, $child->parent->id);
    }

    public function test_children_relationship()
    {
        $parent = Category::create(['type' => 'product', 'name' => 'Medicamentos']);
        Category::create(['type' => 'product', 'name' => 'Antibióticos', 'parent_id' => $parent->id]);
        $this->assertCount(1, $parent->children);
    }
}
