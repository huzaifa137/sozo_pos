<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'shipping_address',
        'shipping_city',
        'delivery_method',
        'delivery_fee',
        'subtotal',
        'tax_total',
        'discount_amount',
        'total',
        'payment_method',
        'payment_reference',
        'payment_status',
        'status',
        'notes',
        'channel',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'delivery_fee' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . date('Ymd') . '-';
        $last = static::where('order_number', 'like', $prefix . '%')
            ->orderByDesc('id')->value('order_number');
        $seq = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'warn',
            'confirmed' => 'blue',
            'processing' => 'blue',
            'ready' => 'yellow',
            'delivered' => 'green',
            'cancelled' => 'red',
            default => 'muted',
        };
    }

    public function getBuyerNameAttribute(): string
    {
        return $this->customer?->name ?? $this->guest_name ?? 'Guest';
    }

    public function getBuyerEmailAttribute(): string
    {
        return $this->customer?->email ?? $this->guest_email ?? '';
    }
}

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'inventory_item_id', 'item_name', 'unit_price', 'quantity', 'line_total'];
    protected $casts = ['unit_price' => 'decimal:2', 'line_total' => 'decimal:2'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    public function inventoryItem()
    {
        return $this->belongsTo(InventoryItem::class);
    }
}