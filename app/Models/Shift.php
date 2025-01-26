<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Shift extends Model
{
    protected $fillable = [
        'date',
        'period',
        'slots'
    ];

    protected $casts = [
        'date' => 'date',
        'slots' => 'array'
    ];

    // Relationships
    public function distributions(): HasMany
    {
        return $this->hasMany(ShiftDistribution::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(ShiftHistory::class);
    }

    // Scopes
    public function scopeForDate($query, string $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }
}
