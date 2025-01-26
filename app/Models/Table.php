<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = [
        'map_template_id',
        'name',
        'capacity',
        'position',
        'active_from',
        'active_until'
    ];

    protected $casts = [
        'position' => 'array',
        'active_from' => 'date',
        'active_until' => 'date'
    ];

    // Relationships
    public function mapTemplate(): BelongsTo
    {
        return $this->belongsTo(MapTemplate::class);
    }

    public function shiftHistories(): HasMany
    {
        return $this->hasMany(ShiftHistory::class);
    }

    // Scopes
    public function scopeActiveAt($query, string $date)
    {
        return $query->where('active_from', '<=', $date)
                    ->where(function($q) use ($date) {
                        $q->whereNull('active_until')
                          ->orWhere('active_until', '>=', $date);
                    });
    }
}
