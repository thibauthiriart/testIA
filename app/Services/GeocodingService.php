<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class GeocodingService
{
    protected string $baseUrl = 'https://nominatim.openstreetmap.org/search';
    
    /**
     * Géocoder une adresse pour obtenir les coordonnées GPS
     */
    public function geocodeAddress(string $address, ?string $city = null, ?string $postalCode = null): ?array
    {
        // Construire l'adresse complète
        $fullAddress = $this->buildFullAddress($address, $city, $postalCode);
        
        // Vérifier le cache d'abord
        $cacheKey = 'geocode_' . md5($fullAddress);
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }
        
        try {
            // Appel à l'API Nominatim avec un délai pour respecter les limites
            sleep(1); // Respecter la limite de 1 req/sec de Nominatim
            
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'PropertyScraper/1.0 (contact@example.com)'
                ])
                ->get($this->baseUrl, [
                    'q' => $fullAddress,
                    'format' => 'json',
                    'limit' => 1,
                    'countrycodes' => 'fr', // Limiter à la France
                    'addressdetails' => 1
                ]);
            
            if ($response->successful()) {
                $results = $response->json();
                
                if (!empty($results)) {
                    $result = $results[0];
                    
                    $coordinates = [
                        'latitude' => (float) $result['lat'],
                        'longitude' => (float) $result['lon'],
                        'display_name' => $result['display_name'] ?? null,
                        'confidence' => $this->calculateConfidence($result, $city)
                    ];
                    
                    // Mettre en cache pour 30 jours
                    Cache::put($cacheKey, $coordinates, now()->addDays(30));
                    
                    Log::info("Geocoded address successfully", [
                        'address' => $fullAddress,
                        'coordinates' => $coordinates
                    ]);
                    
                    return $coordinates;
                }
            }
            
            Log::warning("No geocoding results found", ['address' => $fullAddress]);
            
        } catch (\Exception $e) {
            Log::error("Geocoding failed", [
                'address' => $fullAddress,
                'error' => $e->getMessage()
            ]);
        }
        
        // Mettre en cache l'échec pour éviter de répéter les appels
        Cache::put($cacheKey, null, now()->addHours(6));
        
        return null;
    }
    
    /**
     * Géocoder en utilisant seulement le nom de la ville
     */
    public function geocodeCity(string $city, ?string $postalCode = null): ?array
    {
        $address = $postalCode ? "{$city}, {$postalCode}, France" : "{$city}, France";
        return $this->geocodeAddress($address);
    }
    
    /**
     * Construire l'adresse complète pour le géocodage
     */
    protected function buildFullAddress(string $address, ?string $city = null, ?string $postalCode = null): string
    {
        $parts = [$address];
        
        if ($city) {
            $parts[] = $city;
        }
        
        if ($postalCode) {
            $parts[] = $postalCode;
        }
        
        $parts[] = 'France';
        
        return implode(', ', array_filter($parts));
    }
    
    /**
     * Calculer un score de confiance basé sur la correspondance
     */
    protected function calculateConfidence(array $result, ?string $expectedCity = null): float
    {
        $confidence = 0.5; // Base confidence
        
        // Augmenter la confiance si la ville correspond
        if ($expectedCity && isset($result['address']['city'])) {
            $resultCity = strtolower($result['address']['city']);
            $expectedCityLower = strtolower($expectedCity);
            
            if (str_contains($resultCity, $expectedCityLower) || str_contains($expectedCityLower, $resultCity)) {
                $confidence += 0.3;
            }
        }
        
        // Augmenter la confiance selon le type de lieu
        if (isset($result['class'])) {
            switch ($result['class']) {
                case 'building':
                case 'place':
                    $confidence += 0.2;
                    break;
                case 'highway':
                    $confidence += 0.1;
                    break;
            }
        }
        
        return min(1.0, $confidence);
    }
    
    /**
     * Géocoder plusieurs adresses en lot (avec limitation de débit)
     */
    public function geocodeBatch(array $addresses, int $delaySeconds = 1): array
    {
        $results = [];
        
        foreach ($addresses as $index => $address) {
            if ($index > 0) {
                sleep($delaySeconds); // Respecter les limites de l'API
            }
            
            $result = is_array($address) 
                ? $this->geocodeAddress($address['address'] ?? '', $address['city'] ?? null, $address['postal_code'] ?? null)
                : $this->geocodeAddress($address);
                
            $results[] = $result;
        }
        
        return $results;
    }
}