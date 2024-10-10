<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class My_cart extends Model
{
    protected $fillable = [
        'user_id',
        'product_id',
    ];
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}   
