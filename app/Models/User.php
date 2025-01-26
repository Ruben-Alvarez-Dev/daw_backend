<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Models\Reservation;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'active_until'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'active_until' => 'datetime'
    ];

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Get the reservations for the user.
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    // Scope para obtener solo usuarios activos
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->whereNull('active_until')
              ->orWhere('active_until', '>', now());
        });
    }

    // Scope para validación única solo entre usuarios activos
    public function scopeUniqueActive($query, $field, $value, $ignoreId = null)
    {
        $query->where($field, $value)
              ->where(function($q) {
                  $q->whereNull('active_until')
                    ->orWhere('active_until', '>', now());
              });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query;
    }
}
