<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoyaltyRate extends Model
{
    protected $fillable = [
        'group_name',
        'name',
        'unit',
        'amount',
    ];
}
