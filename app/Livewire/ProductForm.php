<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use Livewire\Attributes\On;
use Livewire\Component;

class ProductForm extends Component
{
    public $productId;
    public $name = '';
    public $sku = '';
    public $category_id = '';
    public $supplier_id = '';
    public $cost_price = '';
    public $sale_price = '';
    public $stock = '';
    public $batch_number = '';
    public $lot_number = '';
    public $expiration_date = '';
    public $is_active = true;

    public $categories = [];
    public $suppliers = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'sku' => 'nullable|string',
        'category_id' => 'nullable|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'cost_price' => 'required|numeric|min:0',
        'sale_price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'is_active' => 'boolean',
    ];

    public function mount($id = null)
    {
        $this->categories = Category::where('type', 'product')->orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('name')->get();
        if ($id) $this->load($id);
    }

    #[On('editProduct')]
    public function load($id)
    {
        $this->productId = $id;
        $product = Product::findOrFail($id);
        $this->name = $product->name;
        $this->sku = $product->sku ?? '';
        $this->category_id = (string) ($product->category_id ?? '');
        $this->supplier_id = (string) ($product->supplier_id ?? '');
        $this->cost_price = (string) $product->cost_price;
        $this->sale_price = (string) $product->sale_price;
        $this->stock = (string) $product->stock;
        $this->batch_number = $product->batch_number ?? '';
        $this->lot_number = $product->lot_number ?? '';
        $this->expiration_date = $product->expiration_date ? $product->expiration_date->format('Y-m-d') : '';
        $this->is_active = $product->is_active;
        $this->categories = Category::where('type', 'product')->orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('name')->get();
    }

    #[On('resetForm')]
    public function resetForm()
    {
        $this->productId = null;
        $this->name = '';
        $this->sku = '';
        $this->category_id = '';
        $this->supplier_id = '';
        $this->cost_price = '';
        $this->sale_price = '';
        $this->stock = '';
        $this->batch_number = '';
        $this->lot_number = '';
        $this->expiration_date = '';
        $this->is_active = true;
        $this->categories = Category::where('type', 'product')->orderBy('name')->get();
        $this->suppliers = Supplier::orderBy('name')->get();
        $this->resetValidation();
    }

    public function save()
    {
        foreach (['sku', 'category_id', 'supplier_id'] as $f) {
            $this->$f = $this->$f ?: null;
        }
        $this->is_active = (bool) $this->is_active;
        $this->validate();

        $data = [
            'name' => $this->name,
            'sku' => $this->sku,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id,
            'cost_price' => $this->cost_price,
            'sale_price' => $this->sale_price,
            'stock' => $this->stock,
            'batch_number' => $this->batch_number ?: null,
            'lot_number' => $this->lot_number ?: null,
            'expiration_date' => $this->expiration_date ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->productId) {
            Product::findOrFail($this->productId)->update($data);
        } else {
            Product::create($data);
        }

        $this->dispatch('product-saved');
        $this->dispatch('close-modal');
    }

    public function render()
    {
        return view('livewire.product-form');
    }
}
