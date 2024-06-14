<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'capacity',
    ];

    /**
     * Get the reservations for the table.
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class);
    }
}