<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    protected $fillable = [
        'layout_data'
    ];

    protected $casts = [
        'layout_data' => 'array'
    ];

    public function templates()
    {
        return $this->hasMany(Template::class);
    }
}
