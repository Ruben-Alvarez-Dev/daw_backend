<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShiftZone extends Model
{
    protected $fillable = ['shift_id', 'zone_id', 'map_id'];

    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function tables(): HasMany
    {
        return $this->hasMany(ShiftZoneTable::class);
    }
}
