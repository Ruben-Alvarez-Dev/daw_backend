<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Zone;
use App\Models\Map;

class InitialMapSetupSeeder extends Seeder
{
    public function run(): void
    {
        // Create default zones
        $salon = Zone::create([
            'name' => 'Salon'
        ]);

        $terrace = Zone::create([
            'name' => 'Terrace'
        ]);

        // Create default maps
        $salonMap = Map::create([
            'name' => 'Default Salon Layout',
            'zone_id' => $salon->id,
            'is_default' => true,
            'content' => [
                'elements' => [
                    [
                        'id' => 'wall_1',
                        'type' => 'wall',
                        'x' => 0,
                        'y' => 0,
                        'width' => 800,
                        'height' => 20,
                        'rotation' => 0
                    ],
                    [
                        'id' => 'wall_2',
                        'type' => 'wall',
                        'x' => 0,
                        'y' => 0,
                        'width' => 20,
                        'height' => 600,
                        'rotation' => 0
                    ]
                ]
            ]
        ]);

        $terraceMap = Map::create([
            'name' => 'Default Terrace Layout',
            'zone_id' => $terrace->id,
            'is_default' => true,
            'content' => [
                'elements' => []
            ]
        ]);
    }
}
