<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tickets extends Model
{
    protected $fillable = [
        'order_id',
        'title',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }

    // Relationship to Staff
    public function staffs()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
    public function addresses()
    {
        return $this->belongsTo(Addresses::class, 'address_id');
    }
}
