<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    protected $fillable = [
        'name',
        'unit',
        'measurement',
        'price',
        'quantity',
        'image',
        'description',
        'profit',
        'category_id',
        'incentives',
    ];
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
    public function categories()
    {
        return $this->belongsTo(categories::class, 'category_id');
    }
}

