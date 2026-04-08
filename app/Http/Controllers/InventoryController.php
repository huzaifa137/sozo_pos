<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    /**
     * Available product categories.
     */
    public const CATEGORIES = [
        'electronics'    => 'Electronics & Gadgets',
        'clothing'       => 'Clothing & Apparel',
        'food_beverage'  => 'Food & Beverages',
        'furniture'      => 'Furniture & Home Decor',
        'stationery'     => 'Stationery & Office',
    ];

    /**
     * Available stock numbers (purchase batches).
     */
    public const STOCK_NUMBERS = [
        'STK-2024-001' => 'STK-2024-001 — January Batch',
        'STK-2024-002' => 'STK-2024-002 — March Batch',
        'STK-2024-003' => 'STK-2024-003 — June Batch',
        'STK-2024-004' => 'STK-2024-004 — September Batch',
        'STK-2024-005' => 'STK-2024-005 — December Batch',
    ];

    /**
     * Display all inventory items.
     */
    public function index(Request $request)
    {
        $query = InventoryItem::query();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by stock number
        if ($request->filled('stock_number')) {
            $query->where('stock_number', $request->stock_number);
        }

        // Search by name
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $items      = $query->latest()->paginate(12)->withQueryString();
        $categories = self::CATEGORIES;
        $stocks     = self::STOCK_NUMBERS;

        return view('inventory.index', compact('items', 'categories', 'stocks'));
    }

    /**
     * Show the form to add a new inventory item.
     */
    public function create()
    {
        $categories = self::CATEGORIES;
        $stocks     = self::STOCK_NUMBERS;

        return view('inventory.create', compact('categories', 'stocks'));
    }

    /**
     * Store a new inventory item.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'selling_price' => 'required|numeric|min:0',
            'buying_price'  => 'required|numeric|min:0',
            'category'      => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'stock_number'  => 'required|in:' . implode(',', array_keys(self::STOCK_NUMBERS)),
            'quantity'      => 'required|integer|min:0',
            'description'   => 'nullable|string|max:1000',
            'image_data'    => 'nullable|string', // base64 from camera
        ]);

        $imagePath = null;

        // Process base64 camera image
        if ($request->filled('image_data')) {
            $imagePath = $this->saveBase64Image($request->image_data);
        }

        InventoryItem::create([
            'name'          => $validated['name'],
            'selling_price' => $validated['selling_price'],
            'buying_price'  => $validated['buying_price'],
            'category'      => $validated['category'],
            'stock_number'  => $validated['stock_number'],
            'quantity'      => $validated['quantity'],
            'description'   => $validated['description'] ?? null,
            'image_path'    => $imagePath,
        ]);

        return redirect()->route('inventory.index')
            ->with('success', 'Item added to inventory successfully!');
    }

    /**
     * Show a single inventory item.
     */
public function show(InventoryItem $inventory)
{
    $categories = self::CATEGORIES;
    return view('inventory.show', [
        'inventoryItem' => $inventory,
        'categories' => $categories
    ]);
}

    /**
     * Show the edit form.
     */
public function edit(InventoryItem $inventory)
{
    $categories = self::CATEGORIES;
    $stocks     = self::STOCK_NUMBERS;

    return view('inventory.edit', [
        'inventoryItem' => $inventory,
        'categories' => $categories,
        'stocks' => $stocks
    ]);
}

    /**
     * Update an inventory item.
     */
   public function update(Request $request, InventoryItem $inventory)
{
    $validated = $request->validate([
        'name'          => 'required|string|max:255',
        'selling_price' => 'required|numeric|min:0',
        'buying_price'  => 'required|numeric|min:0',
        'category'      => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
        'stock_number'  => 'required|in:' . implode(',', array_keys(self::STOCK_NUMBERS)),
        'quantity'      => 'required|integer|min:0',
        'description'   => 'nullable|string|max:1000',
        'image_data'    => 'nullable|string',
    ]);

    $imagePath = $inventory->image_path;

    if ($request->filled('image_data')) {
        if ($inventory->image_path) {
            @unlink(public_path($inventory->image_path)); // FIXED (no Storage anymore)
        }
        $imagePath = $this->saveBase64Image($request->image_data);
    }

    $inventory->update([
        'name'          => $validated['name'],
        'selling_price' => $validated['selling_price'],
        'buying_price'  => $validated['buying_price'],
        'category'      => $validated['category'],
        'stock_number'  => $validated['stock_number'],
        'quantity'      => $validated['quantity'],
        'description'   => $validated['description'] ?? null,
        'image_path'    => $imagePath,
    ]);

    return redirect()->route('inventory.index')
        ->with('success', 'Item updated successfully!');
}

    /**
     * Delete an inventory item.
     */
public function destroy(InventoryItem $inventory)
{
    // Delete image file if it exists
    if (!empty($inventory->image_path)) {
        $imageFullPath = public_path($inventory->image_path);

        if (file_exists($imageFullPath)) {
            unlink($imageFullPath);
        }
    }

    // Delete database record
    $inventory->delete();

    return redirect()->route('inventory.index')
        ->with('success', 'Item removed from inventory.');
}

    /**
     * Save a base64 image string to storage.
     */
    private function saveBase64Image(string $base64Data): string
    {
        if (str_contains($base64Data, ',')) {
            [, $base64Data] = explode(',', $base64Data, 2);
        }

        $imageData = base64_decode($base64Data);

        $filename = Str::uuid() . '.jpg';
        $folder = public_path('uploads/inventory');

        // Create folder if it does not exist
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }

        file_put_contents($folder . '/' . $filename, $imageData);

        return 'uploads/inventory/' . $filename;
    }
}
