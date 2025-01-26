<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftZoneTable extends Model
{
    protected $fillable = ['shift_zone_id', 'table_id', 'position', 'is_available'];

    protected $casts = [
        'position' => 'array',
        'is_available' => 'boolean'
    ];

    public function shiftZone(): BelongsTo
    {
        return $this->belongsTo(ShiftZone::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }
}
