<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Models\Table;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Crear 5 usuarios, incluyendo 1 administrador
        if (User::count() == 0) {
            User::factory()->count(4)->create();
            User::factory()->create(['is_admin' => true]);
        }

        // Crear 12 mesas
        Table::factory()->count(12)->create();

    }
}