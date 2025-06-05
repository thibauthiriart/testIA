<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $properties = [
            [
                'source' => 'seloger',
                'source_id' => '1001',
                'title' => 'Appartement 3 pièces avec balcon',
                'description' => 'Bel appartement lumineux au 3ème étage avec vue dégagée',
                'price' => 450000,
                'surface' => 75,
                'rooms' => 3,
                'bedrooms' => 2,
                'city' => 'Paris',
                'postal_code' => '75015',
                'department' => '75',
                'latitude' => 48.8434,
                'longitude' => 2.2888,
                'property_type' => 'apartment',
                'transaction_type' => 'sale',
                'url' => 'https://www.seloger.com/annonces/1001.htm',
                'images' => ['https://via.placeholder.com/400x300'],
                'additional_info' => ['etage' => '3', 'ascenseur' => 'oui'],
                'is_active' => true,
                'scraped_at' => now(),
            ],
            [
                'source' => 'seloger',
                'source_id' => '1002',
                'title' => 'Maison 5 pièces avec jardin',
                'description' => 'Maison familiale avec grand jardin et garage',
                'price' => 680000,
                'surface' => 120,
                'rooms' => 5,
                'bedrooms' => 4,
                'city' => 'Versailles',
                'postal_code' => '78000',
                'department' => '78',
                'latitude' => 48.8014,
                'longitude' => 2.1301,
                'property_type' => 'house',
                'transaction_type' => 'sale',
                'url' => 'https://www.seloger.com/annonces/1002.htm',
                'images' => ['https://via.placeholder.com/400x300'],
                'additional_info' => ['terrain' => '500m²', 'garage' => '2 places'],
                'is_active' => true,
                'scraped_at' => now(),
            ],
            [
                'source' => 'seloger',
                'source_id' => '1003',
                'title' => 'Studio meublé proche métro',
                'description' => 'Studio entièrement rénové et meublé, idéal pour étudiant',
                'price' => 850,
                'surface' => 25,
                'rooms' => 1,
                'bedrooms' => 0,
                'city' => 'Paris',
                'postal_code' => '75011',
                'department' => '75',
                'latitude' => 48.8566,
                'longitude' => 2.3785,
                'property_type' => 'studio',
                'transaction_type' => 'rent',
                'url' => 'https://www.seloger.com/annonces/1003.htm',
                'images' => ['https://via.placeholder.com/400x300'],
                'additional_info' => ['meublé' => 'oui', 'charges' => '50€'],
                'is_active' => true,
                'scraped_at' => now(),
            ],
            [
                'source' => 'seloger',
                'source_id' => '1004',
                'title' => 'Appartement 2 pièces rénové',
                'description' => 'Appartement entièrement rénové avec cuisine équipée',
                'price' => 1200,
                'surface' => 45,
                'rooms' => 2,
                'bedrooms' => 1,
                'city' => 'Lyon',
                'postal_code' => '69003',
                'department' => '69',
                'latitude' => 45.7640,
                'longitude' => 4.8357,
                'property_type' => 'apartment',
                'transaction_type' => 'rent',
                'url' => 'https://www.seloger.com/annonces/1004.htm',
                'images' => ['https://via.placeholder.com/400x300'],
                'additional_info' => ['étage' => '2', 'chauffage' => 'individuel'],
                'is_active' => true,
                'scraped_at' => now(),
            ],
            [
                'source' => 'seloger',
                'source_id' => '1005',
                'title' => 'Villa contemporaine avec piscine',
                'description' => 'Magnifique villa moderne avec piscine et vue mer',
                'price' => 1200000,
                'surface' => 200,
                'rooms' => 6,
                'bedrooms' => 4,
                'city' => 'Nice',
                'postal_code' => '06000',
                'department' => '06',
                'latitude' => 43.7102,
                'longitude' => 7.2620,
                'property_type' => 'villa',
                'transaction_type' => 'sale',
                'url' => 'https://www.seloger.com/annonces/1005.htm',
                'images' => ['https://via.placeholder.com/400x300'],
                'additional_info' => ['piscine' => 'oui', 'vue' => 'mer'],
                'is_active' => true,
                'scraped_at' => now(),
            ],
        ];

        foreach ($properties as $property) {
            Property::create($property);
        }
    }
}
