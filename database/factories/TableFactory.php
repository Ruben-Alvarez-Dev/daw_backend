<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TableFactory extends Factory
{
    public function definition()
    {
        return [
            'number' => $this->faker->unique()->numberBetween(1, 20), // Número de mesa autoincremental de 1 a 999
            'capacity' => $this->faker->randomElement([2, 4, 8, 10]),
        ];
    }
}

