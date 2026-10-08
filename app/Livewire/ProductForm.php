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
    public $fractional_cost_price = '';
    public $fractional_sale_price = '';
    public $stock_unit = 'un';
    public $dispensing_unit = '';
    public $package_quantity = 1;
    public $allows_fractional = false;
    public $requires_container_tracking = false;
    public $opened_use_days = '';
    public $reconstituted_use_days = '';
    public $batch_number = '';
    public $lot_number = '';
    public $expiration_date = '';
    public $is_active = true;

    public $categories = [];
    public $suppliers = [];

    protected function rules()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'cost_price' => 'required|numeric|min:0',
            'sale_price' => 'required|numeric|min:0',
            'fractional_cost_price' => 'nullable|numeric|min:0',
            'fractional_sale_price' => 'nullable|numeric|min:0',
            'stock_unit' => 'required|in:ml,mg,g,comprimido,dose,un',
            'dispensing_unit' => 'nullable|in:ml,mg,g,comprimido,dose,un',
            'package_quantity' => 'required|numeric|gt:0',
            'allows_fractional' => 'boolean',
            'requires_container_tracking' => 'boolean',
            'opened_use_days' => 'nullable|integer|min:1',
            'reconstituted_use_days' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ];
        if (!$this->productId) {
            $rules['stock'] = 'required|integer|min:0';
        }
        return $rules;
    }

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
        $this->fractional_cost_price = (string) ($product->fractional_cost_price ?? '');
        $this->fractional_sale_price = (string) ($product->fractional_sale_price ?? '');
        $this->stock_unit = $product->stock_unit ?? 'un';
        $this->dispensing_unit = $product->dispensing_unit ?? '';
        $this->package_quantity = (string) ($product->package_quantity ?? 1);
        $this->allows_fractional = (bool) $product->allows_fractional;
        $this->requires_container_tracking = (bool) $product->requires_container_tracking;
        $this->opened_use_days = (string) ($product->opened_use_days ?? '');
        $this->reconstituted_use_days = (string) ($product->reconstituted_use_days ?? '');
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
        $this->fractional_cost_price = '';
        $this->fractional_sale_price = '';
        $this->stock_unit = 'un';
        $this->dispensing_unit = '';
        $this->package_quantity = 1;
        $this->allows_fractional = false;
        $this->requires_container_tracking = false;
        $this->opened_use_days = '';
        $this->reconstituted_use_days = '';
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
        foreach (['category_id', 'supplier_id'] as $f) {
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
            'fractional_cost_price' => $this->fractional_cost_price ?: null,
            'fractional_sale_price' => $this->fractional_sale_price ?: null,
            'stock_unit' => $this->stock_unit,
            'dispensing_unit' => $this->dispensing_unit ?: null,
            'package_quantity' => $this->package_quantity,
            'allows_fractional' => (bool) $this->allows_fractional,
            'requires_container_tracking' => (bool) $this->requires_container_tracking,
            'opened_use_days' => $this->opened_use_days ?: null,
            'reconstituted_use_days' => $this->reconstituted_use_days ?: null,
            'batch_number' => $this->batch_number ?: null,
            'lot_number' => $this->lot_number ?: null,
            'expiration_date' => $this->expiration_date ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->productId) {
            unset($data['stock']);
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
