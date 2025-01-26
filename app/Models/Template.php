<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    protected $fillable = [
        'name',
        'layout_data',
        'is_default',
        'shift_date',
        'shift_type'
    ];

    protected $casts = [
        'layout_data' => 'array',
        'is_default' => 'boolean',
        'shift_date' => 'date'
    ];

    public function map()
    {
        return $this->belongsTo(Map::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeForShift($query, $date, $type)
    {
        return $query->where('shift_date', $date)
                    ->where('shift_type', $type);
    }
}
