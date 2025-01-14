<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tables_ids',
        'guests',
        'datetime',
        'status',
        'user_info'
    ];

    protected $casts = [
        'tables_ids' => 'array',
        'datetime' => 'datetime',
        'user_info' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tables()
    {
        return $this->belongsToMany(Table::class, null, 'id', 'id')
                    ->whereIn('id', $this->tables_ids);
    }
}
