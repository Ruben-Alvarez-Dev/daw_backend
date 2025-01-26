<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MapHistory extends Model
{
    protected $fillable = ['map_id', 'user_id', 'content', 'action'];

    protected $casts = [
        'content' => 'array'
    ];

    public function map(): BelongsTo
    {
        return $this->belongsTo(Map::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
