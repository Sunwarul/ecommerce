<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BaseUnit;
use App\Models\Unit;

class BaseUnitAndUnitSeeder extends Seeder
{
    public function run(): void
    {
        $baseUnits = [
            'Length' => [
                ['name' => 'Meter', 'short_code' => 'm'],
                ['name' => 'Centimeter', 'short_code' => 'cm'],
                ['name' => 'Millimeter', 'short_code' => 'mm'],
                ['name' => 'Kilometer', 'short_code' => 'km'],
            ],
            'Weight' => [
                ['name' => 'Kilogram', 'short_code' => 'kg'],
                ['name' => 'Gram', 'short_code' => 'g'],
                ['name' => 'Milligram', 'short_code' => 'mg'],
                ['name' => 'Ton', 'short_code' => 'ton'],
            ],
            'Volume' => [
                ['name' => 'Liter', 'short_code' => 'L'],
                ['name' => 'Milliliter', 'short_code' => 'ml'],
                ['name' => 'Cubic Meter', 'short_code' => 'm³'],
            ],
            'Time' => [
                ['name' => 'Second', 'short_code' => 's'],
                ['name' => 'Minute', 'short_code' => 'min'],
                ['name' => 'Hour', 'short_code' => 'h'],
            ],
            'Temperature' => [
                ['name' => 'Celsius', 'short_code' => '°C'],
                ['name' => 'Fahrenheit', 'short_code' => '°F'],
                ['name' => 'Kelvin', 'short_code' => 'K'],
            ],
        ];

        foreach ($baseUnits as $baseName => $units) {
            // Create base unit
            $base = BaseUnit::create([
                'name' => $baseName,
                'status' => true,
            ]);

            // Create its units
            foreach ($units as $unit) {
                Unit::create([
                    'name' => $unit['name'],
                    'short_code' => $unit['short_code'],
                    'status' => true,
                    'base_unit_id' => $base->id,
                ]);
            }
        }
    }
}
