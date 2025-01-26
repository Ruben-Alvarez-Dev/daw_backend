<?php

namespace Database\Seeders;

use App\Models\Map;
use Illuminate\Database\Seeder;

class MapSeeder extends Seeder
{
    public function run()
    {
        // Crear el mapa base si no existe
        if (!Map::first()) {
            Map::create([
                'layout_data' => [
                    'elements' => []
                ]
            ]);
        }
    }
}
