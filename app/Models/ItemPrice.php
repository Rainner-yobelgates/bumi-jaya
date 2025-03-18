<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPrice extends Model
{
    protected $fillable = ['item_id', 'attr', 'price'];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
