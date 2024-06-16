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
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
    ];

    /**
     * The default value for the status attribute.
     *
     * @var string
     */
    protected $attributes = [
        'status' => 'available',
    ];

    /**
     * Get the reservations for the table.
     */
    public function reservations()
    {
        return $this->belongsToMany(Reservation::class);
    }
}