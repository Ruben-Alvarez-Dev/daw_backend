<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    private $configFile = 'restaurant-config.json';

    public function getConfig()
    {
        if (!Storage::exists($this->configFile)) {
            // Configuración por defecto
            $defaultConfig = [
                'totalCapacity' => 0,
                'timeEstimateSmall' => 60,
                'timeEstimateLarge' => 90,
                'openingHours' => [
                    'afternoon' => [
                        'open' => '13:00',
                        'close' => '16:00'
                    ],
                    'evening' => [
                        'open' => '20:00',
                        'close' => '23:30'
                    ]
                ]
            ];
            Storage::put($this->configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
            return response()->json($defaultConfig);
        }

        $config = json_decode(Storage::get($this->configFile), true);
        return response()->json($config);
    }

    public function updateConfig(Request $request)
    {
        $config = $request->all();
        Storage::put($this->configFile, json_encode($config, JSON_PRETTY_PRINT));
        return response()->json(['message' => 'Configuración actualizada']);
    }
}
