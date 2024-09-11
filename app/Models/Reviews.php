<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviews extends Model
{
    protected $fillable = [
        'product_id',
        'description',
        'image1',
        'image2',
        'image3',
        'ratings',
    ];
    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}   
