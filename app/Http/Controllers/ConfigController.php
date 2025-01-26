<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    private $configFile = 'restaurant-config.json';

    private function getDefaultConfig()
    {
        return [
            'totalCapacity' => 0,
            'timeEstimateSmall' => 60,
            'timeEstimateLarge' => 90,
            'timeInterval' => 15,
            'simultaneousTables' => 2,
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
    }

    public function getConfig()
    {
        $defaultConfig = $this->getDefaultConfig();

        if (!Storage::exists($this->configFile)) {
            Storage::put($this->configFile, json_encode($defaultConfig, JSON_PRETTY_PRINT));
            return response()->json($defaultConfig);
        }

        $storedConfig = json_decode(Storage::get($this->configFile), true);
        
        // Hacer merge recursivo para mantener todos los campos, incluyendo arrays anidados
        $config = array_replace_recursive($defaultConfig, $storedConfig);
        
        // Guardar la configuración actualizada
        Storage::put($this->configFile, json_encode($config, JSON_PRETTY_PRINT));
        
        return response()->json($config);
    }

    public function updateConfig(Request $request)
    {
        $config = json_decode(Storage::get($this->configFile), true) ?? $this->getDefaultConfig();
        $newConfig = array_replace_recursive($config, $request->all());
        
        Storage::put($this->configFile, json_encode($newConfig, JSON_PRETTY_PRINT));
        
        return response()->json($newConfig);
    }
}
