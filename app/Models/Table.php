<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Table extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity'
    ];

    protected $casts = [
        'isActive' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
