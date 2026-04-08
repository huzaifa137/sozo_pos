<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'image_path',
        'selling_price',
        'buying_price',
        'category',
        'stock_number',
        'quantity',
        'description',
    ];

    protected $casts = [
        'selling_price' => 'decimal:2',
        'buying_price'  => 'decimal:2',
    ];

    /**
     * Calculate the profit margin.
     */
    public function getProfitMarginAttribute(): float
    {
        if ($this->buying_price == 0) return 0;
        return round((($this->selling_price - $this->buying_price) / $this->buying_price) * 100, 2);
    }

    /**
     * Get the full image URL.
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image_path) {
            return asset('storage/' . $this->image_path);
        }
        return asset('images/no-image.png');
    }
}
