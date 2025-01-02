<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReservationPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Reservation $reservation)
    {
        return $user->isAdmin() || $user->id === $reservation->user_id;
    }

    public function update(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id;
    }

    public function delete(User $user, Reservation $reservation)
    {
        return $user->id === $reservation->user_id;
    }
}
