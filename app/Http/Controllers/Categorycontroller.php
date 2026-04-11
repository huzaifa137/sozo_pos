<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('inventoryItems')->latest()->get();
        return view('categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'display_name' => 'required|string|max:150',
            'code'         => 'required|string|max:60|unique:categories,code',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        $data['code']      = Str::slug($data['code']);   // normalise
        $data['is_active'] = $request->boolean('is_active', true);

        Category::create($data);

        return redirect()->route('categories.index')
            ->with('success', "Category \"{$data['display_name']}\" added.");
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:100',
            'display_name' => 'required|string|max:150',
            'code'         => 'required|string|max:60|unique:categories,code,' . $category->id,
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        $data['code']      = Str::slug($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);

        $category->update($data);

        return redirect()->route('categories.index')
            ->with('success', "Category \"{$data['display_name']}\" updated.");
    }

    public function destroy(Category $category)
    {
        if ($category->inventoryItems()->exists()) {
            return back()->with('error',
                "Cannot delete \"{$category->display_name}\" — it has linked inventory items.");
        }

        $name = $category->display_name;
        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', "Category \"{$name}\" deleted.");
    }
}