<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $fillable = [
        'name',
        'merk',
        'attr',
        'quantity',
        'price',
        'total_price',
    ];
}
