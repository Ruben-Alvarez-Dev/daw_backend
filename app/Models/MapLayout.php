<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MapLayout extends Model
{
    protected $fillable = [
        'name',
        'is_default',
        'layout'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'layout' => 'json'
    ];
}
