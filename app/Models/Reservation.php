<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\Table;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'datetime',
        'guests',
        'status',
        'user_id',
        'table_id',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'guests' => 'integer',
        'is_active' => 'boolean'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
