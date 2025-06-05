<?php

namespace App\Console\Commands;

use App\Models\Property;
use App\Services\GeocodingService;
use Illuminate\Console\Command;

class GeocodeProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:geocode {--limit=50 : Number of properties to geocode} {--force : Force geocoding even if coordinates exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Geocode properties to add latitude and longitude coordinates';

    protected GeocodingService $geocodingService;

    public function __construct(GeocodingService $geocodingService)
    {
        parent::__construct();
        $this->geocodingService = $geocodingService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');
        
        $this->info("Starting geocoding process...");
        
        // Récupérer les propriétés sans coordonnées (ou toutes si --force)
        $query = Property::where('is_active', true);
        
        if (!$force) {
            $query->where(function($q) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude');
            });
        }
        
        $properties = $query->limit($limit)->get();
        
        if ($properties->isEmpty()) {
            $this->info('No properties to geocode.');
            return 0;
        }
        
        $this->info("Found {$properties->count()} properties to geocode.");
        
        $progressBar = $this->output->createProgressBar($properties->count());
        $progressBar->start();
        
        $geocoded = 0;
        $failed = 0;
        
        foreach ($properties as $property) {
            try {
                // Construire l'adresse pour le géocodage
                $address = $this->buildPropertyAddress($property);
                
                if (empty($address)) {
                    $this->newLine();
                    $this->warn("Skipping property {$property->id}: No address information");
                    $failed++;
                    $progressBar->advance();
                    continue;
                }
                
                // Géocoder l'adresse
                $coordinates = $this->geocodingService->geocodeAddress(
                    $address,
                    $property->city,
                    $property->postal_code
                );
                
                if ($coordinates && $coordinates['confidence'] > 0.3) {
                    // Mettre à jour la propriété avec les coordonnées
                    $property->update([
                        'latitude' => $coordinates['latitude'],
                        'longitude' => $coordinates['longitude']
                    ]);
                    
                    $geocoded++;
                } else {
                    // Essayer avec seulement la ville
                    $cityCoordinates = $this->geocodingService->geocodeCity(
                        $property->city,
                        $property->postal_code
                    );
                    
                    if ($cityCoordinates) {
                        $property->update([
                            'latitude' => $cityCoordinates['latitude'],
                            'longitude' => $cityCoordinates['longitude']
                        ]);
                        
                        $geocoded++;
                    } else {
                        $failed++;
                    }
                }
                
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Error geocoding property {$property->id}: " . $e->getMessage());
                $failed++;
            }
            
            $progressBar->advance();
            
            // Petite pause pour respecter les limites de l'API
            usleep(1100000); // 1.1 secondes
        }
        
        $progressBar->finish();
        $this->newLine(2);
        
        $this->info("Geocoding completed!");
        $this->info("Successfully geocoded: {$geocoded}");
        $this->info("Failed: {$failed}");
        
        return 0;
    }
    
    /**
     * Construire l'adresse d'une propriété pour le géocodage
     */
    protected function buildPropertyAddress(Property $property): string
    {
        $addressParts = [];
        
        // Essayer d'extraire l'adresse du titre ou de la description
        $text = $property->title . ' ' . $property->description;
        
        // Chercher des patterns d'adresse dans le texte
        if (preg_match('/(\d+[a-zA-Z]?\s+(?:rue|avenue|boulevard|place|impasse|chemin|route)[^,.\n]*)/i', $text, $matches)) {
            $addressParts[] = trim($matches[1]);
        }
        
        // Ajouter la ville si disponible
        if ($property->city) {
            $addressParts[] = $property->city;
        }
        
        // Si pas d'adresse trouvée, utiliser seulement la ville
        if (empty($addressParts) && $property->city) {
            return $property->city;
        }
        
        return implode(', ', $addressParts);
    }
}
