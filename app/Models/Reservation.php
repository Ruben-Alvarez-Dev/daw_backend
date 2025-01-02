<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tables_ids',
        'guests',
        'datetime',
        'comments'
    ];

    protected $casts = [
        'tables_ids' => 'array',
        'datetime' => 'datetime',
        'isActive' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
