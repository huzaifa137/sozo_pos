<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InventoryItem extends Model
{
    protected $fillable = [
        'name','slug','sku','barcode','image_path','selling_price','buying_price',
        'category','stock_number','quantity','low_stock_threshold','description',
        'description_long','size','color','model','expiry_date','batch_number',
        'tax_rate','published','featured','views',
    ];

    protected $casts = [
        'selling_price'      => 'decimal:2',
        'buying_price'       => 'decimal:2',
        'tax_rate'           => 'decimal:2',
        'expiry_date'        => 'date',
        'published'          => 'boolean',
        'featured'           => 'boolean',
    ];

    // Auto-generate slug on save
    protected static function boot()
    {
        parent::boot();
        static::saving(function ($item) {
            if (empty($item->slug)) {
                $item->slug = static::makeUniqueSlug($item->name, $item->id);
            }
        });
    }

    public static function makeUniqueSlug(string $name, ?int $exceptId = null): string
    {
        $slug = Str::slug($name);
        $base = $slug;
        $i    = 2;
        while (
            static::where('slug', $slug)
                ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
                ->exists()
        ) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }

    public function saleItems()  { return $this->hasMany(\App\Models\SaleItem::class); }
    public function orderItems() { return $this->hasMany(\App\Models\OrderItem::class); }

    public function isLowStock(): bool
    {
        return $this->quantity > 0 && $this->quantity <= $this->low_stock_threshold;
    }

    public function isExpiringSoon(): bool
    {
        return $this->expiry_date && $this->expiry_date->lte(now()->addDays(30));
    }

    public function getProfitMarginAttribute(): float
    {
        if ($this->buying_price == 0) return 0;
        return round((($this->selling_price - $this->buying_price) / $this->buying_price) * 100, 2);
    }

    // Scope for barcode/SKU lookup in POS search
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('barcode', $term)
                     ->orWhere('sku', $term);
    }
}