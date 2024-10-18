<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StaffTransactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'staff_id',
        'type',
        'amount',
        'datetime',
    ];

    // Corrected Relationship Method (singular)
    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }
}
