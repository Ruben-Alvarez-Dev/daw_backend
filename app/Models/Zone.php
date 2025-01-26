<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Zone extends Model
{
    protected $fillable = ['name'];

    public function maps(): HasMany
    {
        return $this->hasMany(Map::class);
    }

    public function defaultMap()
    {
        return $this->maps()->where('is_default', true)->first();
    }

    public function shiftZones(): HasMany
    {
        return $this->hasMany(ShiftZone::class);
    }
}
