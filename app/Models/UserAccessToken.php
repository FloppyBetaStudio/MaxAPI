<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccessToken extends Model
{
    use HasFactory;

    protected $casts = [
        'dt_create' => 'datetime',
        'dt_expire' => 'datetime',
    ];
}
