<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name','phone','email','address','loyalty_points','total_spent','loyalty_tier'];

    protected $casts = ['total_spent' => 'decimal:2'];

    public function sales() { return $this->hasMany(Sale::class); }

    public function updateTier(): void
    {
        $this->loyalty_tier = match(true) {
            $this->total_spent >= 5000000 => 'gold',
            $this->total_spent >= 1000000 => 'silver',
            default                       => 'bronze',
        };
        $this->save();
    }
}
