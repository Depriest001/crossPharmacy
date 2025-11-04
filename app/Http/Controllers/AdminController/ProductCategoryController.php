<?php

namespace App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;

class ProductCategoryController extends Controller
{
    public function index()
    {
        $categories = Category::latest()->get();
        return view('admin.category.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'description' => 'required',
        ]);

        Category::create($request->only('name', 'description'));

        return redirect()->back()->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('admin.category.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'description' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        $category->update($request->all());

        return redirect()->route('category.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();

        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

}
