<?php

namespace Database\Factories;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Table;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'table_id' => Table::factory(),
            'date' => Carbon::instance($this->faker->dateTimeBetween('now', '+30 days'))->format('Y-m-d'),
            'time' => $this->faker->time('H:i:00'),
            'guests' => $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->optional()->sentence(),
            'status' => 'confirmed'
        ];
    }

    public function confirmed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'confirmed'
            ];
        });
    }

    public function cancelled()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'cancelled'
            ];
        });
    }

    public function completed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'completed'
            ];
        });
    }
}
