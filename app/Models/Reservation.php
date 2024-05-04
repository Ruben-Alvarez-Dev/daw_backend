<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $table = 'reservation';

    protected $fillable = [
        'user_id',
        'table_id',
        'reservation_date',
        'start_time',
        'num_guests',
        'status',
    ];

}
