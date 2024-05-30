<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'userName',
        'password',
        'followedStreamers',
    ];

    protected $casts = [
        'followedStreamers' => 'array',
    ];
}
