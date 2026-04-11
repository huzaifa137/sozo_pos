<?php
// app/Models/InventoryItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InventoryItem extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'barcode',
        'image_path',
        'selling_price',
        'buying_price',
        'category_code',
        'batch_code', // New foreign keys
        'category',
        'stock_number', // For backward compatibility
        'quantity',
        'low_stock_threshold',
        'description',
        'description_long',
        'size',
        'color',
        'model',
        'expiry_date',
        'batch_number',
        'tax_rate',
        'published',
        'featured',
        'views',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'buying_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'expiry_date' => 'date',
        'published' => 'boolean',
        'featured' => 'boolean',
    ];

    // Auto-generate slug on save
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            // Auto-generate slug
            if (empty($item->slug)) {
                $item->slug = static::makeUniqueSlug($item->name, $item->id);
            }

            // Auto-populate category and stock_number for backward compatibility
            if (empty($item->category) && !empty($item->category_code)) {
                $item->category = Category::where('code', $item->category_code)->value('name');
            }

            if (empty($item->stock_number) && !empty($item->batch_code)) {
                $item->stock_number = StockBatch::where('code', $item->batch_code)->value('batch_number');
            }
        });
    }

    public static function makeUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i = 2;

        while (
            static::where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    // Relationships
    public function categoryRelation()
    {
        return $this->belongsTo(Category::class, 'category_code', 'code');
    }

    public function stockBatch()
    {
        return $this->belongsTo(StockBatch::class, 'batch_code', 'code');
    }

    public function saleItems()
    {
        return $this->hasMany(\App\Models\SaleItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    // Accessors
    public function getCategoryDisplayNameAttribute()
    {
        return $this->categoryRelation?->display_name ?? $this->category;
    }

    public function getBatchDisplayNameAttribute()
    {
        return $this->stockBatch?->display_name ?? $this->stock_number;
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->buying_price == 0)
            return 0;
        return round((($this->selling_price - $this->buying_price) / $this->buying_price) * 100, 2);
    }

    // Helper methods
    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->low_stock_threshold;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->lte(now()->addDays(30));
    }

    // Scopes
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
            ->orWhere('barcode', $term)
            ->orWhere('sku', $term);
    }

    public function scopePublished($query)
    {
        return $query->where('published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('quantity', '>', 0);
    }

    public function scopeLowStock($query)
    {
        return $query->where('quantity', '>', 0)
            ->whereColumn('quantity', '<=', 'low_stock_threshold');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('quantity', 0);
    }

    public function scopeByCategory($query, $categoryCode)
    {
        return $query->where('category_code', $categoryCode);
    }

    public function scopeByBatch($query, $batchCode)
    {
        return $query->where('batch_code', $batchCode);
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', now()->addDays($days));
    }
}