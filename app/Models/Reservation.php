<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_CANCELED = 'canceled';

    use HasFactory;

    protected $primaryKey = 'reservation_id';

    protected $fillable = [
        'user_id',
        'table_ids',
        'pax_number',
        'date',
        'time',
        'status',
    ];

    protected $casts = [
        'table_ids' => 'array',
    ];

    // No necesitamos relaciones Eloquent aquí

    // Método para obtener los objetos Table relacionados (opcional)
    public function getTables()
    {
        return Table::whereIn('table_id', $this->table_ids)->get();
    }

    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        if ($key === 'table_ids' && is_array($value)) {
            return $value;
        } elseif ($key === 'table_ids' && $value !== null) {
            return unserialize($value);
        }

        return $value;
    }
}