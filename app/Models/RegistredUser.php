<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistredUser extends Model
{
    use HasFactory;

    // Nombre de la tabla en la base de datos
    protected $table = 'registredUsers';

    // Campos que pueden ser asignados masivamente
    protected $fillable = [
        'username',
        'password',
        'followedStreamers'
    ];

    // Campos que deben ser ocultos en los arrays
    protected $hidden = [
        'password',
    ];

    // Los campos que son de tipo JSON
    protected $casts = [
        'followedStreamers' => 'array',
    ];
}
