<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawals extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'amount',
        'status',
        'datetime',
    ];

    public function staffs()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }
}
