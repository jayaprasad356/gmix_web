<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reward_points extends Model
{
    protected $fillable = [
        'points',
        'name',
        'description',
    ];
}
