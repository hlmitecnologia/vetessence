<?php

namespace App\Livewire;

use App\Models\Branch;
use App\Models\Category;
use Livewire\Attributes\On;
use Livewire\Component;

class CategoryForm extends Component
{
    public $categoryId;
    public $name = '';
    public $type = 'service';
    public $description = '';
    public $parent_id = '';
    public $parentCategories = [];
    public $branch_id = '';
    public $branches = [];

    protected $rules = [
        'name' => 'required|string|max:100',
        'type' => 'required|in:product,service,vaccine',
        'description' => 'nullable|string',
        'parent_id' => 'nullable|exists:categories,id',
        'branch_id' => 'nullable|exists:branches,id',
    ];

    public function mount($categoryId = null)
    {
        $this->parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();
        $this->branches = Branch::orderBy('name')->get();

        if ($categoryId) {
            $this->loadCategory($categoryId);
        }
    }

    public function mount($categoryId = null)
    {
        $this->parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        if ($categoryId) {
            $this->loadCategory($categoryId);
        }
    }

    #[On('editCategory')]
    public function loadCategory($id)
    {
        $this->categoryId = $id;
        $category = Category::findOrFail($id);
        $this->name = $category->name;
        $this->type = $category->type;
        $this->description = $category->description ?? '';
        $this->parent_id = (string) ($category->parent_id ?? '');
        $this->branch_id = (string) ($category->branch_id ?? '');
        $this->parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('name')
            ->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->categoryId = null;
        $this->name = '';
        $this->type = 'service';
        $this->description = '';
        $this->parent_id = '';
        $this->branch_id = '';
        $this->parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();
        $this->resetValidation();
    }

    public function save()
    {
        $this->parent_id = $this->parent_id ?: null;
        $this->description = $this->description ?: null;
        $this->branch_id = $this->branch_id ?: null;
        $this->validate();

        if ($this->categoryId) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'type' => $this->type,
                'description' => $this->description,
                'parent_id' => $this->parent_id,
                'branch_id' => $this->branch_id,
            ]);
        } else {
            Category::create([
                'name' => $this->name,
                'type' => $this->type,
                'description' => $this->description,
                'parent_id' => $this->parent_id,
                'branch_id' => $this->branch_id,
            ]);
        }

        $this->dispatch('category-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.category-form');
    }
}
