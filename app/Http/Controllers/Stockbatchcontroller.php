<?php

namespace App\Http\Controllers;

use App\Models\StockBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StockBatchController extends Controller
{
    public function index()
    {
        $batches = StockBatch::withCount('inventoryItems')->latest()->get();
        return view('stock_batches.index', compact('batches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'batch_number' => 'required|string|max:60|unique:stock_batches,batch_number',
            'display_name' => 'required|string|max:150',
            'code'         => 'required|string|max:60|unique:stock_batches,code',
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        $data['code']      = Str::upper(Str::slug($data['code'], '-'));
        $data['is_active'] = $request->boolean('is_active', true);

        StockBatch::create($data);

        return redirect()->route('stock-batches.index')
            ->with('success', "Stock batch \"{$data['batch_number']}\" added.");
    }

    public function update(Request $request, StockBatch $stockBatch)
    {
        $data = $request->validate([
            'batch_number' => 'required|string|max:60|unique:stock_batches,batch_number,' . $stockBatch->id,
            'display_name' => 'required|string|max:150',
            'code'         => 'required|string|max:60|unique:stock_batches,code,' . $stockBatch->id,
            'description'  => 'nullable|string|max:500',
            'is_active'    => 'boolean',
        ]);

        $data['code']      = Str::upper(Str::slug($data['code'], '-'));
        $data['is_active'] = $request->boolean('is_active', true);

        $stockBatch->update($data);

        return redirect()->route('stock-batches.index')
            ->with('success', "Batch \"{$data['batch_number']}\" updated.");
    }

    public function destroy(StockBatch $stockBatch)
    {
        if ($stockBatch->inventoryItems()->exists()) {
            return back()->with('error',
                "Cannot delete \"{$stockBatch->batch_number}\" — it has linked inventory items.");
        }

        $name = $stockBatch->batch_number;
        $stockBatch->delete();

        return redirect()->route('stock-batches.index')
            ->with('success', "Batch \"{$name}\" deleted.");
    }
}