<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Users extends Authenticatable
{
    use Notifiable;

    protected $guard = 'users';

    protected $table = 'users';


    protected $fillable = [
        'name', 'email', 'mobile','points','total_points','staff_id', // Add 'mobile' to the fillable fields
    ];

    public function addresses()
    {
        return $this->hasMany(Addresses::class, 'user_id');
    }
    public function staff()
    {
        return $this->belongsTo(Staffs::class, 'staff_id');
    }


    public function findForPassport($mobile)
    {
        return $this->where('mobile', $mobile)->first();
    }
    public function getFullname()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getAvatar()
    {
        return 'https://www.gravatar.com/avatar/' . md5($this->email);
    }
}
