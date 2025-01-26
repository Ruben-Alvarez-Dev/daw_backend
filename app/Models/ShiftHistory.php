<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShiftHistory extends Model
{
    protected $fillable = [
        'shift_id',
        'table_id',
        'reservation_id',
        'planned_time',
        'actual_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'planned_time' => 'datetime',
        'actual_time' => 'datetime',
        'notes' => 'array'
    ];

    // Relationships
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    // Scopes
    public function scopeForStatus($query, string $status)
    {
        return $query->where('status', $status);
    }
}
