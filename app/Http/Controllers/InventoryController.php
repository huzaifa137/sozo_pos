<?php
// app/Http/Controllers/InventoryController.php (updated)

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use App\Models\Category;
use App\Models\Subcategory;
use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoryItem::with(['categoryRelation', 'stockBatch']);

        if ($request->filled('category')) {
            $query->where('category_code', $request->category);
        }
        if ($request->filled('stock_number')) {
            $query->where('batch_code', $request->stock_number);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('sku', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filter === 'low') {
            $query->whereColumn('quantity', '<=', 'low_stock_threshold')
                ->where('quantity', '>', 0);
        }
        if ($request->filter === 'out') {
            $query->where('quantity', 0);
        }
        if ($request->filter === 'expiring') {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '<=', now()->addDays(30));
        }

        $items = $query->latest()->paginate(12)->withQueryString();

        // Get from database instead of constants
        $categories = Category::getForDropdown();
        $stocks = StockBatch::getForDropdown();

        $alerts = InventoryItem::where(function ($q) {
            $q->whereColumn('quantity', '<=', 'low_stock_threshold')
                ->orWhere('quantity', 0);
        })->count();

        return view('inventory.index', compact('items', 'categories', 'stocks', 'alerts'));
    }

    public function create()
    {
        $categories = Category::getForDropdown();
        $stocks = StockBatch::getForDropdown();

        // group subcategories by category_code
        $subcategories = Subcategory::where('is_active', true)
            ->get()
            ->groupBy('category_code');

        return view('inventory.create', compact('categories', 'stocks', 'subcategories'));
    }

    public function store(Request $request)
    {
        // Get category codes for validation
        $categoryCodes = Category::active()->pluck('code')->implode(',');
        $batchCodes = StockBatch::active()->pluck('code')->implode(',');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:inventory_items',
            'barcode' => 'nullable|string|unique:inventory_items',
            'selling_price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'category' => 'required|in:' . $categoryCodes,
            'subcategory' => 'required|exists:subcategories,code',
            'stock_number' => 'required|in:' . $batchCodes,
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image_data' => 'nullable|string',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Map category and stock_number to category_code and batch_code
        $validated['category_code'] = $validated['category'];
        $validated['batch_code'] = $validated['stock_number'];

        // Keep the original fields for backward compatibility
        $validated['category'] = Category::where('code', $validated['category_code'])->value('name') ?? $validated['category'];
        $validated['stock_number'] = StockBatch::where('code', $validated['batch_code'])->value('batch_number') ?? $validated['stock_number'];

        $imagePath = null;
        if ($request->filled('image_data')) {
            $imagePath = $this->saveBase64Image($request->image_data);
        }

        InventoryItem::create(array_merge($validated, ['image_path' => $imagePath]));

        return redirect()->route('inventory.index')->with('success', 'Item added to inventory!');
    }

    public function edit(InventoryItem $inventory)
    {
        $categories = Category::getForDropdown();
        $stocks = StockBatch::getForDropdown();

        return view('inventory.edit', [
            'inventoryItem' => $inventory,
            'categories' => $categories,
            'stocks' => $stocks
        ]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $categoryCodes = Category::active()->pluck('code')->implode(',');
        $batchCodes = StockBatch::active()->pluck('code')->implode(',');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|unique:inventory_items,sku,' . $inventory->id,
            'barcode' => 'nullable|string|unique:inventory_items,barcode,' . $inventory->id,
            'selling_price' => 'required|numeric|min:0',
            'buying_price' => 'required|numeric|min:0',
            'category' => 'required|in:' . $categoryCodes,
            'stock_number' => 'required|in:' . $batchCodes,
            'quantity' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'description' => 'nullable|string|max:1000',
            'image_data' => 'nullable|string',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:100',
            'expiry_date' => 'nullable|date',
            'batch_number' => 'nullable|string',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        $validated['category_code'] = $validated['category'];
        $validated['batch_code'] = $validated['stock_number'];

        $validated['category'] = Category::where('code', $validated['category_code'])->value('name') ?? $validated['category'];
        $validated['stock_number'] = StockBatch::where('code', $validated['batch_code'])->value('batch_number') ?? $validated['stock_number'];

        $imagePath = $inventory->image_path;
        if ($request->filled('image_data')) {
            if ($inventory->image_path && file_exists(public_path($inventory->image_path))) {
                @unlink(public_path($inventory->image_path));
            }
            $imagePath = $this->saveBase64Image($request->image_data);
        }

        $inventory->update(array_merge($validated, ['image_path' => $imagePath]));

        return redirect()->route('inventory.index')->with('success', 'Item updated!');
    }

    public function destroy(InventoryItem $inventory)
    {
        if (!empty($inventory->image_path)) {
            @unlink(public_path($inventory->image_path));
        }
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Item removed.');
    }

    public function show(InventoryItem $inventory)
    {
        $inventory->load(['categoryRelation', 'stockBatch']);
        $categories = Category::getForDropdown();

        return view('inventory.show', [
            'inventoryItem' => $inventory,
            'categories' => $categories
        ]);
    }

    private function saveBase64Image(string $base64Data): string
    {
        if (str_contains($base64Data, ',')) {
            [, $base64Data] = explode(',', $base64Data, 2);
        }
        $filename = Str::uuid() . '.jpg';
        $folder = public_path('uploads/inventory');
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        file_put_contents($folder . '/' . $filename, base64_decode($base64Data));
        return 'uploads/inventory/' . $filename;
    }
}