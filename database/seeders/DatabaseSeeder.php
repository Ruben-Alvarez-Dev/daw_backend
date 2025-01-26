<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Table;
use App\Models\MapTemplate;
use Database\Seeders\MapSeeder;
use Database\Seeders\InitialMapSetupSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('admin'),
            'phone' => '123456789',
            'role' => 'admin'
        ]);

        // Create tables
        for ($i = 1; $i <= 10; $i++) {
            Table::create([
                'name' => 'Mesa ' . $i,
                'capacity' => rand(2, 8)
            ]);
        }

        $this->call([
            InitialMapSetupSeeder::class
        ]);
    }
}
