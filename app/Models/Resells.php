<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resells extends Model
{
    protected $fillable = [
        'user_id',
        'place',
        'qualification',
        'experience',
        'gender',
        'age',
    ];
    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}   
