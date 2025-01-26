<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MapTemplate extends Model
{
    protected $fillable = [
        'name',
        'zone',
        'is_default',
        'elements'
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'elements' => 'array'
    ];

    // Relationships
    public function tables(): HasMany
    {
        return $this->hasMany(Table::class);
    }

    public function shiftDistributions(): HasMany
    {
        return $this->hasMany(ShiftDistribution::class);
    }

    // Scopes
    public function scopeDefault($query, string $zone)
    {
        return $query->where('zone', $zone)
                    ->where('is_default', true);
    }
}
