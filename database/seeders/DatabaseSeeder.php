<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Table;
use App\Models\MapTemplate;
use Database\Seeders\MapSeeder;

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

        // Create default map template
        MapTemplate::create([
            'name' => 'Default Template',
            'zone' => 'salon',
            'is_default' => true,
            'elements' => []
        ]);

        // Create some tables
        Table::create([
            'name' => 'Mesa 1',
            'capacity' => 2,
            'status' => 'available',
            'map_template_id' => 1,
            'position' => ['x' => 0, 'y' => 0],
            'active_from' => now()
        ]);

        Table::create([
            'name' => 'Mesa 2',
            'capacity' => 4,
            'status' => 'available',
            'map_template_id' => 1,
            'position' => ['x' => 1, 'y' => 0],
            'active_from' => now()
        ]);

        Table::create([
            'name' => 'Mesa 3',
            'capacity' => 6,
            'status' => 'available',
            'map_template_id' => 1,
            'position' => ['x' => 2, 'y' => 0],
            'active_from' => now()
        ]);

        $this->call([
            MapSeeder::class,
        ]);
    }
}
