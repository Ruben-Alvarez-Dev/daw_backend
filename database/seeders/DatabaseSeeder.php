<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Table;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'role' => 'admin',
            'visits' => 0
        ]);

        // Create some tables
        Table::create([
            'number' => 1,
            'capacity' => 2,
            'status' => 'available'
        ]);

        Table::create([
            'number' => 2,
            'capacity' => 4,
            'status' => 'available'
        ]);

        Table::create([
            'number' => 3,
            'capacity' => 6,
            'status' => 'available'
        ]);
    }
}
