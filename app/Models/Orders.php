<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
        'address_id',
        'price',
        'delivery_charges',
        'payment_mode',
        'total_price',
        'quantity',
        'ratings',
        'reviews',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function staffs()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }

     public function addresses()
     {
         return $this->belongsTo(Addresses::class, 'address_id');
     }
     // The relationship for the product ordered
     public function product()
     {
         return $this->belongsTo(Products::class, 'product_id');
     }
     
    
}
