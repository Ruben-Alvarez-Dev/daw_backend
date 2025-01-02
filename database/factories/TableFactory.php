<?php

namespace Database\Factories;

use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    protected $model = Table::class;

    public function definition()
    {
        return [
            'number' => $this->faker->unique()->numberBetween(1, 100),
            'capacity' => $this->faker->numberBetween(2, 12),
            'status' => $this->faker->randomElement(['available', 'occupied', 'reserved']),
            'created_by' => User::factory()
        ];
    }

    public function available()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'available'
            ];
        });
    }

    public function occupied()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'occupied'
            ];
        });
    }

    public function reserved()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'reserved'
            ];
        });
    }
}
