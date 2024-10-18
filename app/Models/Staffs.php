<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staffs extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'mobile', 
        'password', 
        'incentives', 
        'total_incentives',
    ];
    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }
    public function transactions()
    {
        return $this->hasMany(StaffTransactions::class, 'staff_id');
    }
}
