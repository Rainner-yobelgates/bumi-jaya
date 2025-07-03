<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'total_price',
        'discount',
        'total',
    ];

    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
}
