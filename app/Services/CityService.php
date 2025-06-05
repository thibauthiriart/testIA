<?php

namespace App\Services;

use App\Models\City;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class CityService
{
    protected string $apiUrl = 'https://geo.api.gouv.fr/communes';
    
    /**
     * Trouver ou créer une ville par son nom
     */
    public function findOrCreateCity(string $cityName): ?City
    {
        // Extraire le code postal s'il est présent
        $postalCode = $this->extractPostalCode($cityName);
        
        // Nettoyer le nom de la ville
        $cleanCityName = $this->cleanCityName($cityName);
        
        if (empty($cleanCityName)) {
            return null;
        }
        
        // Chercher d'abord dans la base de données
        $city = $this->findCityInDatabase($cleanCityName, $postalCode);
        
        if ($city) {
            Log::info("City found in database: {$cleanCityName}" . ($postalCode ? " ({$postalCode})" : ""));
            return $city;
        }
        
        // Si pas trouvée, chercher via l'API
        $cityData = $this->fetchCityFromApi($cleanCityName, $postalCode);
        
        if ($cityData) {
            // Créer la ville en base
            $city = $this->createCityFromApiData($cityData);
            Log::info("City created from API: {$cleanCityName}" . ($postalCode ? " ({$postalCode})" : ""));
            return $city;
        }
        
        Log::warning("City not found: {$cleanCityName}" . ($postalCode ? " ({$postalCode})" : ""));
        return null;
    }
    
    /**
     * Chercher une ville dans la base de données
     */
    protected function findCityInDatabase(string $cityName, ?string $postalCode = null): ?City
    {
        if ($postalCode) {
            // Essayer avec différentes variantes du nom
            $variations = $this->generateNameVariations($cityName);
            
            foreach ($variations as $variation) {
                $city = City::whereRaw('LOWER(REPLACE(name, "-", " ")) = LOWER(REPLACE(?, "-", " "))', [$variation])
                    ->where('postal_code', $postalCode)
                    ->first();
                    
                if ($city) {
                    return $city;
                }
            }
            
            // Recherche avec similarité sans accents
            $cleanName = $this->removeAccents($cityName);
            $city = City::whereRaw('LOWER(REPLACE(REPLACE(name, "-", " "), "é", "e")) = LOWER(REPLACE(REPLACE(?, "-", " "), "é", "e"))', [$cleanName])
                ->where('postal_code', $postalCode)
                ->first();
                
            if ($city) {
                return $city;
            }
        }
        
        // Fallback: recherche approximative par nom uniquement
        $cleanName = $this->removeAccents($cityName);
        return City::whereRaw('LOWER(REPLACE(REPLACE(name, "-", " "), "é", "e")) LIKE LOWER(REPLACE(REPLACE(?, "-", " "), "é", "e"))', ['%' . $cleanName . '%'])
            ->first();
    }
    
    /**
     * Normaliser le nom de ville pour la recherche
     */
    protected function normalizeCityNameForSearch(string $cityName): string
    {
        $normalized = trim($cityName);
        
        // Supprimer les accents
        $normalized = $this->removeAccents($normalized);
        
        // Remplacer les espaces par des tirets
        $normalized = str_replace(' ', '-', $normalized);
        
        // Mettre en minuscules pour la comparaison
        $normalized = strtolower($normalized);
        
        return $normalized;
    }
    
    /**
     * Générer des variations du nom de ville
     */
    protected function generateNameVariations(string $cityName): array
    {
        $variations = [];
        $base = trim($cityName);
        
        // Version avec tirets (avec et sans accents)
        $withDashes = str_replace(' ', '-', $base);
        $variations[] = $withDashes;
        $variations[] = $this->removeAccents($withDashes);
        
        // Version avec espaces (avec et sans accents)
        $withSpaces = str_replace('-', ' ', $base);
        $variations[] = $withSpaces;
        $variations[] = $this->removeAccents($withSpaces);
        
        // Version originale
        $variations[] = $base;
        $variations[] = $this->removeAccents($base);
        
        return array_unique($variations);
    }
    
    /**
     * Supprimer les accents d'une chaîne
     */
    protected function removeAccents(string $string): string
    {
        $unwanted_array = [
            'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
            'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I',
            'Ð'=>'D', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'TH', 'ß'=>'ss',
            'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'ae', 'ç'=>'c',
            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i',
            'ð'=>'d', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
            'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'þ'=>'th', 'ÿ'=>'y'
        ];
        
        return strtr($string, $unwanted_array);
    }
    
    /**
     * Récupérer les données de la ville depuis l'API du gouvernement
     */
    protected function fetchCityFromApi(string $cityName, ?string $postalCode = null): ?array
    {
        $cacheKey = 'city_api_' . md5($cityName . ($postalCode ?? ''));
        
        // Vérifier le cache
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return $cached;
        }
        
        try {
            $params = [
                'nom' => $cityName,
                'fields' => 'nom,code,codesPostaux,centre,population,departement',
                'format' => 'json',
                'limit' => 1
            ];
            
            // Ajouter le code postal si disponible pour être plus précis
            if ($postalCode) {
                $params['codePostal'] = $postalCode;
            }
            
            $response = Http::timeout(10)->get($this->apiUrl, $params);
            
            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data) && is_array($data)) {
                    $cityData = $data[0];
                    
                    // Mettre en cache pour 7 jours
                    Cache::put($cacheKey, $cityData, now()->addDays(7));
                    
                    return $cityData;
                }
            }
            
        } catch (\Exception $e) {
            Log::error("Error fetching city from API: {$cityName}" . ($postalCode ? " ({$postalCode})" : ""), [
                'error' => $e->getMessage()
            ]);
        }
        
        // Mettre en cache l'échec pour éviter de répéter les appels
        Cache::put($cacheKey, null, now()->addHours(2));
        
        return null;
    }
    
    /**
     * Créer une ville à partir des données de l'API
     */
    protected function createCityFromApiData(array $cityData): City
    {
        $postalCodes = $cityData['codesPostaux'] ?? [];
        $center = $cityData['centre'] ?? [];
        $department = $cityData['departement'] ?? [];
        
        return City::create([
            'name' => $cityData['nom'],
            'code' => $cityData['code'] ?? null,
            'postal_code' => !empty($postalCodes) ? $postalCodes[0] : null,
            'latitude' => $center['coordinates'][1] ?? null,
            'longitude' => $center['coordinates'][0] ?? null,
            'population' => $cityData['population'] ?? 0,
            'department_id' => $this->findOrCreateDepartment($department)
        ]);
    }
    
    /**
     * Trouver ou créer le département
     */
    protected function findOrCreateDepartment(array $departmentData): ?int
    {
        if (empty($departmentData['code']) || empty($departmentData['nom'])) {
            return null;
        }
        
        $department = \App\Models\Department::firstOrCreate(
            ['code' => $departmentData['code']],
            ['name' => $departmentData['nom']]
        );
        
        return $department->id;
    }
    
    /**
     * Nettoyer le nom de la ville
     */
    protected function cleanCityName(string $cityName): string
    {
        // Supprimer les espaces en début/fin
        $cleaned = trim($cityName);
        
        // Si le format contient un code postal (Ville - 12345), extraire seulement le nom
        if (preg_match('/^(.+?)\s*-\s*(\d{5})$/', $cleaned, $matches)) {
            $cleaned = trim($matches[1]);
            // Le code postal est dans $matches[2] si on en a besoin plus tard
        }
        
        // Supprimer les caractères indésirables
        $cleaned = preg_replace('/[^\p{L}\s\-\']/u', '', $cleaned);
        
        // Normaliser les espaces
        $cleaned = preg_replace('/\s+/', ' ', $cleaned);
        
        return $cleaned;
    }
    
    /**
     * Extraire le code postal depuis le nom de ville formaté
     */
    protected function extractPostalCode(string $cityName): ?string
    {
        // Si le format contient un code postal (Ville - 12345), l'extraire
        if (preg_match('/^.+?\s*-\s*(\d{5})$/', trim($cityName), $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Rechercher des villes dans un rayon donné
     */
    public function findCitiesInRadius(float $latitude, float $longitude, int $radiusKm = 20): \Illuminate\Database\Eloquent\Collection
    {
        // SQLite doesn't support HAVING on non-aggregate queries, use whereRaw instead
        return City::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->whereRaw("
                ( 6371 * acos( cos( radians(?) ) * 
                cos( radians( latitude ) ) * 
                cos( radians( longitude ) - radians(?) ) + 
                sin( radians(?) ) * 
                sin( radians( latitude ) ) ) ) < ?
            ", [$latitude, $longitude, $latitude, $radiusKm])
            ->selectRaw("*, 
                ( 6371 * acos( cos( radians(?) ) * 
                cos( radians( latitude ) ) * 
                cos( radians( longitude ) - radians(?) ) + 
                sin( radians(?) ) * 
                sin( radians( latitude ) ) ) ) AS distance
            ", [$latitude, $longitude, $latitude])
            ->orderBy('distance')
            ->get();
    }
    
    /**
     * Obtenir toutes les propriétés dans un rayon autour d'une ville
     */
    public function getPropertiesInRadius(City $centerCity, int $radiusKm = 20): \Illuminate\Database\Eloquent\Collection
    {
        // Trouver toutes les villes dans le rayon
        $citiesInRadius = $this->findCitiesInRadius(
            $centerCity->latitude, 
            $centerCity->longitude, 
            $radiusKm
        );
        
        $cityIds = $citiesInRadius->pluck('id')->toArray();
        $cityIds[] = $centerCity->id; // Inclure la ville centrale
        
        // Récupérer toutes les propriétés de ces villes
        return \App\Models\Property::whereIn('city_id', $cityIds)
            ->where('is_active', true)
            ->with('cityModel')
            ->get();
    }
}