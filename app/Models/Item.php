<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name', 'merk'];

    public function itemPrice()
    {
        return $this->hasMany(ItemPrice::class);
    }
}
