<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function inventoryItems()
    {
        return $this->hasMany(InventoryItem::class, 'category_code', 'code');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    // Helper methods
    public static function getForDropdown()
    {
        return self::active()
            ->orderBy('display_name')
            ->pluck('display_name', 'code');
    }

    public function getItemCountAttribute()
    {
        return $this->inventoryItems()->count();
    }

    public function getTotalValueAttribute()
    {
        return $this->inventoryItems()
            ->selectRaw('SUM(quantity * selling_price) as total')
            ->value('total') ?? 0;
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class, 'category_code', 'code');
    }
}