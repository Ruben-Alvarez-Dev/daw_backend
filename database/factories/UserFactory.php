<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'), // Reemplaza 'password' con una contraseña segura
            'phone' => $this->faker->phoneNumber(),
            'is_admin' => $this->faker->boolean(10), // 10% de probabilidad de ser administrador
        ];
    }
}