<?php

namespace Tests\Feature\Livewire;

use App\Models\Branch;
use App\Models\Category;
use Livewire\Livewire;
use Tests\ModuleTestCase;

class CategoryFormTest extends ModuleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->loginAs('admin');
    }

    public function test_can_create(): void
    {
        $branch = Branch::factory()->create();

        Livewire::test('category-form')
            ->set('name', 'Vacina Antirrábica')
            ->set('type', 'vaccine')
            ->set('branch_id', (string) $branch->id)
            ->call('save')
            ->assertDispatched('category-saved');

        $this->assertDatabaseHas('categories', ['name' => 'Vacina Antirrábica']);
    }

    public function test_validates_required_fields(): void
    {
        Livewire::test('category-form')
            ->set('type', '')
            ->call('save')
            ->assertHasErrors(['name', 'type']);
    }

    public function test_validates_type_enum(): void
    {
        Livewire::test('category-form')
            ->set('name', 'Test')
            ->set('type', 'invalid')
            ->call('save')
            ->assertHasErrors(['type']);
    }

    public function test_can_edit(): void
    {
        $category = Category::factory()->create(['name' => 'Exames Laboratoriais']);

        Livewire::test('category-form')
            ->dispatch('editCategory', id: $category->id)
            ->assertSet('name', 'Exames Laboratoriais')
            ->set('name', 'Exames de Imagem')
            ->call('save')
            ->assertDispatched('category-saved');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Exames de Imagem']);
    }

    public function test_reset_form(): void
    {
        Livewire::test('category-form')
            ->set('name', 'Temporary')
            ->set('type', 'product')
            ->dispatch('resetForm')
            ->assertSet('name', '')
            ->assertSet('type', 'service')
            ->assertSet('description', '')
            ->assertSet('parent_id', '')
            ->assertSet('branch_id', '');
    }
}
