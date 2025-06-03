<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CityCoordinatesSeeder extends Seeder
{
    public function run(): void
    {
        $coordinates = [
            'Paris' => ['latitude' => 48.8566, 'longitude' => 2.3522],
            'Lyon' => ['latitude' => 45.7640, 'longitude' => 4.8357],
            'Marseille' => ['latitude' => 43.2965, 'longitude' => 5.3698],
            'Toulouse' => ['latitude' => 43.6047, 'longitude' => 1.4442],
            'Nice' => ['latitude' => 43.7102, 'longitude' => 7.2620],
            'Nantes' => ['latitude' => 47.2184, 'longitude' => -1.5536],
            'Strasbourg' => ['latitude' => 48.5734, 'longitude' => 7.7521],
            'Montpellier' => ['latitude' => 43.6108, 'longitude' => 3.8767],
            'Bordeaux' => ['latitude' => 44.8378, 'longitude' => -0.5792],
            'Lille' => ['latitude' => 50.6292, 'longitude' => 3.0573],
            'Rennes' => ['latitude' => 48.1173, 'longitude' => -1.6778],
            'Reims' => ['latitude' => 49.2583, 'longitude' => 4.0317],
            'Le Havre' => ['latitude' => 49.4944, 'longitude' => 0.1079],
            'Saint-Étienne' => ['latitude' => 45.4397, 'longitude' => 4.3872],
            'Toulon' => ['latitude' => 43.1242, 'longitude' => 5.9280],
            'Grenoble' => ['latitude' => 45.1885, 'longitude' => 5.7245],
            'Dijon' => ['latitude' => 47.3220, 'longitude' => 5.0415],
            'Angers' => ['latitude' => 47.4784, 'longitude' => -0.5632],
            'Nîmes' => ['latitude' => 43.8367, 'longitude' => 4.3601],
            'Aix-en-Provence' => ['latitude' => 43.5297, 'longitude' => 5.4474],
        ];

        foreach ($coordinates as $cityName => $coords) {
            City::where('name', 'LIKE', "%{$cityName}%")
                ->update($coords);
        }
    }
}