<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    protected $fillable = [
        'delivery_charges',
        'customer_support_number',
        'privacy_policy',
        'terms_conditions',
        'refund_policy',
        'upi_id',
    ];
}   
