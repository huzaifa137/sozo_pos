<?php

namespace App\Http\Controllers;

use App\Models\InventoryItem;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public const CATEGORIES = [
        'electronics'   => 'Electronics & Gadgets',
        'clothing'      => 'Clothing & Apparel',
        'food_beverage' => 'Food & Beverages',
        'furniture'     => 'Furniture & Home Decor',
        'stationery'    => 'Stationery & Office',
    ];

    public const STOCK_NUMBERS = [
        'STK-2024-001' => 'STK-2024-001 — January Batch',
        'STK-2024-002' => 'STK-2024-002 — March Batch',
        'STK-2024-003' => 'STK-2024-003 — June Batch',
        'STK-2024-004' => 'STK-2024-004 — September Batch',
        'STK-2024-005' => 'STK-2024-005 — December Batch',
    ];

    public function index(Request $request)
    {
        $query = InventoryItem::query();

        if ($request->filled('category'))     { $query->where('category', $request->category); }
        if ($request->filled('stock_number')) { $query->where('stock_number', $request->stock_number); }
        if ($request->filled('search'))       { $query->where(fn($q) => $q->where('name','like','%'.$request->search.'%')->orWhere('sku','like','%'.$request->search.'%')->orWhere('barcode','like','%'.$request->search.'%')); }
        if ($request->filter === 'low')       { $query->whereColumn('quantity','<=','low_stock_threshold')->where('quantity','>',0); }
        if ($request->filter === 'out')       { $query->where('quantity',0); }
        if ($request->filter === 'expiring')  { $query->whereNotNull('expiry_date')->where('expiry_date','<=',now()->addDays(30)); }

        $items      = $query->latest()->paginate(12)->withQueryString();
        $categories = self::CATEGORIES;
        $stocks     = self::STOCK_NUMBERS;
        $alerts     = InventoryItem::where(fn($q) => $q->whereColumn('quantity','<=','low_stock_threshold')->orWhere('quantity',0))->count();

        return view('inventory.index', compact('items', 'categories', 'stocks', 'alerts'));
    }

    public function create()
    {
        $categories = self::CATEGORIES;
        $stocks     = self::STOCK_NUMBERS;
        return view('inventory.create', compact('categories', 'stocks'));
    }

    public function store(Request $request)
    {
        
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'nullable|string|unique:inventory_items',
            'barcode'            => 'nullable|string|unique:inventory_items',
            'selling_price'      => 'required|numeric|min:0',
            'buying_price'       => 'required|numeric|min:0',
            'category'           => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'stock_number'       => 'required|in:' . implode(',', array_keys(self::STOCK_NUMBERS)),
            'quantity'           => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'description'        => 'nullable|string|max:1000',
            'image_data'         => 'nullable|string',
            'size'               => 'nullable|string|max:50',
            'color'              => 'nullable|string|max:50',
            'model'              => 'nullable|string|max:100',
            'expiry_date'        => 'nullable|date',
            'batch_number'       => 'nullable|string',
            'tax_rate'           => 'nullable|numeric|min:0|max:100',
        ]);

        $imagePath = null;
        if ($request->filled('image_data')) {
            $imagePath = $this->saveBase64Image($request->image_data);
        }

        InventoryItem::create(array_merge($validated, ['image_path' => $imagePath]));

        return redirect()->route('inventory.index')->with('success', 'Item added to inventory!');
    }

    public function edit(InventoryItem $inventory)
    {
        $categories = self::CATEGORIES;
        $stocks     = self::STOCK_NUMBERS;
        return view('inventory.edit', ['inventoryItem' => $inventory, 'categories' => $categories, 'stocks' => $stocks]);
    }

    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'sku'                => 'nullable|string|unique:inventory_items,sku,'.$inventory->id,
            'barcode'            => 'nullable|string|unique:inventory_items,barcode,'.$inventory->id,
            'selling_price'      => 'required|numeric|min:0',
            'buying_price'       => 'required|numeric|min:0',
            'category'           => 'required|in:' . implode(',', array_keys(self::CATEGORIES)),
            'stock_number'       => 'required|in:' . implode(',', array_keys(self::STOCK_NUMBERS)),
            'quantity'           => 'required|integer|min:0',
            'low_stock_threshold'=> 'required|integer|min:0',
            'description'        => 'nullable|string|max:1000',
            'image_data'         => 'nullable|string',
            'size'               => 'nullable|string|max:50',
            'color'              => 'nullable|string|max:50',
            'model'              => 'nullable|string|max:100',
            'expiry_date'        => 'nullable|date',
            'batch_number'       => 'nullable|string',
            'tax_rate'           => 'nullable|numeric|min:0|max:100',
        ]);

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
        return view('inventory.show', ['inventoryItem' => $inventory, 'categories' => self::CATEGORIES]);
    }

    private function saveBase64Image(string $base64Data): string
    {
        if (str_contains($base64Data, ',')) {
            [, $base64Data] = explode(',', $base64Data, 2);
        }
        $filename = Str::uuid() . '.jpg';
        $folder   = public_path('uploads/inventory');
        if (!file_exists($folder)) mkdir($folder, 0755, true);
        file_put_contents($folder . '/' . $filename, base64_decode($base64Data));
        return 'uploads/inventory/' . $filename;
    }
}
