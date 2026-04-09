<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'receipt_number','user_id','customer_id','subtotal','tax_total',
        'discount_amount','total','amount_paid','change_given',
        'payment_method','payment_reference','status','notes',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'tax_total'       => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'amount_paid'     => 'decimal:2',
        'change_given'    => 'decimal:2',
    ];

    public function user()     { return $this->belongsTo(User::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function items()    { return $this->hasMany(SaleItem::class); }

    public static function generateReceiptNumber(): string
    {
        $prefix = 'RCP-' . date('Ymd') . '-';
        $last   = static::where('receipt_number', 'like', $prefix . '%')
                        ->orderByDesc('id')->value('receipt_number');
        $seq    = $last ? ((int) substr($last, -4)) + 1 : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id','inventory_item_id','item_name','unit_price',
        'quantity','tax_rate','discount','line_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'tax_rate'   => 'decimal:2',
        'discount'   => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function sale()          { return $this->belongsTo(Sale::class); }
    public function inventoryItem() { return $this->belongsTo(InventoryItem::class); }
}
