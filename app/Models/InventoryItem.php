<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = [
        'name','sku','barcode','image_path','selling_price','buying_price',
        'category','stock_number','quantity','low_stock_threshold','description',
        'size','color','model','expiry_date','batch_number','tax_rate',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'buying_price'  => 'decimal:2',
        'tax_rate'      => 'decimal:2',
        'expiry_date'   => 'date',
    ];

    public function saleItems() { return $this->hasMany(SaleItem::class); }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
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

    // Scope for barcode / SKU lookup (used in POS)
    public function scopeSearch($query, string $term)
    {
        return $query->where('name', 'like', "%{$term}%")
                     ->orWhere('barcode', $term)
                     ->orWhere('sku', $term);
    }
}
