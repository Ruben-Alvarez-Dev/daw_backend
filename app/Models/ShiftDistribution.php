<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftDistribution extends Model
{
    protected $fillable = [
        'shift_id',
        'map_template_id',
        'zone',
        'table_positions'
    ];

    protected $casts = [
        'table_positions' => 'array'
    ];

    // Relationships
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function mapTemplate(): BelongsTo
    {
        return $this->belongsTo(MapTemplate::class);
    }

    // Scopes
    public function scopeForZone($query, string $zone)
    {
        return $query->where('zone', $zone);
    }
}
