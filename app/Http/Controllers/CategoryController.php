<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        if ($request->type) {
            $query->where('type', $request->type);
        }

        if ($request->parent_id === 'null') {
            $query->whereNull('parent_id');
        } elseif ($request->parent_id) {
            $query->where('parent_id', $request->parent_id);
        }

        $categories = $query->with('parent')->orderBy('type')->orderBy('name')->paginate(30);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();
        return view('categories.create', compact('parentCategories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:product,service,vaccine',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria cadastrada!');
    }

    public function show(Category $category)
    {
        $category->load('children');
        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'type' => 'required|in:product,service,vaccine',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        $category->update($validated);

        return redirect()->route('categories.index')->with('success', 'Categoria atualizada!');
    }

    public function destroy(Category $category)
    {
        if ($category->children()->count() > 0) {
            return back()->with('error', 'Categoria possui subcategorias.');
        }

        if ($category->products()->count() > 0 || $category->services()->count() > 0) {
            return back()->with('error', 'Categoria possui itens vinculados.');
        }

        $category->delete();
        return redirect()->route('categories.index')->with('success', 'Categoria excluída!');
    }
}
