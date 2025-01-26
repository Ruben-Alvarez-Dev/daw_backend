<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Map extends Model
{
    protected $fillable = ['name', 'zone_id', 'content', 'is_default'];

    protected $casts = [
        'content' => 'array',
        'is_default' => 'boolean'
    ];

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(MapHistory::class);
    }

    public function shiftZones(): HasMany
    {
        return $this->hasMany(ShiftZone::class);
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($map) {
            if ($map->is_default) {
                // Si este mapa se está marcando como default, desmarcamos cualquier otro
                static::where('zone_id', $map->zone_id)
                    ->where('id', '!=', $map->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
