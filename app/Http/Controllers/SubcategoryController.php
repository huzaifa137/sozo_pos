<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubcategoryController extends Controller
{
    public function index()
    {
        $subcategories = Subcategory::with('category')
            ->withCount('inventoryItems')
            ->latest()
            ->get();
        
        $categories = Category::where('is_active', true)
            ->orderBy('display_name')
            ->get();
        
        return view('subcategories.index', compact('subcategories', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category_code' => 'required|string|exists:categories,code',
            'name'          => 'required|string|max:100',
            'display_name'  => 'required|string|max:150',
            'code'          => 'required|string|max:60|unique:subcategories,code',
            'description'   => 'nullable|string|max:500',
            'is_active'     => 'boolean',
        ]);

        $data['code']      = Str::slug($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);
        
        unset($data['description']); // Not in subcategories table

        Subcategory::create($data);

        return redirect()->route('subcategories.index')
            ->with('success', "Subcategory \"{$data['display_name']}\" added.");
    }

    public function update(Request $request, Subcategory $subcategory)
    {
        $data = $request->validate([
            'category_code' => 'required|string|exists:categories,code',
            'name'          => 'required|string|max:100',
            'display_name'  => 'required|string|max:150',
            'code'          => 'required|string|max:60|unique:subcategories,code,' . $subcategory->id,
            'description'   => 'nullable|string|max:500',
            'is_active'     => 'boolean',
        ]);

        $data['code']      = Str::slug($data['code']);
        $data['is_active'] = $request->boolean('is_active', true);
        
        unset($data['description']); // Not in subcategories table

        $subcategory->update($data);

        return redirect()->route('subcategories.index')
            ->with('success', "Subcategory \"{$data['display_name']}\" updated.");
    }

    public function destroy(Subcategory $subcategory)
    {
        if ($subcategory->inventoryItems()->exists()) {
            return back()->with('error',
                "Cannot delete \"{$subcategory->display_name}\" — it has linked inventory items.");
        }

        $name = $subcategory->display_name;
        $subcategory->delete();

        return redirect()->route('subcategories.index')
            ->with('success', "Subcategory \"{$name}\" deleted.");
    }
}