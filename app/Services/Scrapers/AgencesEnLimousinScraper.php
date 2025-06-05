<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;
use App\Services\GeocodingService;
use App\Services\CityService;

class AgencesEnLimousinScraper extends AbstractScraper
{
    protected string $baseUrl = 'https://www.agencesenlimousin.com';
    protected string $source = 'agencesenlimousin';
    protected GeocodingService $geocodingService;
    protected CityService $cityService;
    
    public function __construct(GeocodingService $geocodingService = null, CityService $cityService = null)
    {
        parent::__construct();
        $this->geocodingService = $geocodingService ?: app(GeocodingService::class);
        $this->cityService = $cityService ?: app(CityService::class);
    }
    
    public function scrapeListingPage(string $url): array
    {
        $crawler = $this->fetchPage($url);
        
        if (!$crawler) {
            return [];
        }
        
        $properties = [];
        
        $crawler->filter('.annonce-v18')->each(function (Crawler $node) use (&$properties, $url) {
            $property = $this->parseProperty($node, $url, []);
            if ($property) {
                $properties[] = $property;
            }
        });
        
        return $properties;
    }
    
    public function scrapePropertyDetails(string $url): ?array
    {
        $crawler = $this->fetchPage($url);
        
        if (!$crawler) {
            return null;
        }
        
        return $this->parsePropertyDetailsPage($crawler, $url);
    }
    
    protected function parseProperty(Crawler $crawler, string $url, array $params = []): ?array
    {
        try {
            // Extract title
            $title = $this->extractText($crawler, 'h3 a');
            if (!$title) {
                $title = $this->extractText($crawler, '.annonce-titre');
            }
            if (!$title) {
                $title = $this->extractText($crawler, '.title-line-2');
            }
            
            // Extract price - try multiple selectors
            $priceText = $this->extractText($crawler, '.price');
            if (!$priceText) {
                $priceText = $this->extractText($crawler, '.g-font-weight-600');
            }
            if (!$priceText) {
                $priceText = $this->extractText($crawler, '.annonce-prix');
            }
            if (!$priceText) {
                $priceText = $this->extractText($crawler, '.prix');
            }
            
            $price = $this->cleanPrice($priceText);
            
            if (!$title || !$price) {
                return null;
            }
            
            // Extract property link for source_id
            $link = null;
            try {
                $link = $crawler->filter('a')->first()->attr('href');
            } catch (\Exception $e) {
                // No link found
            }
            
            $sourceId = $this->extractSourceId($link);
            
            if (!$sourceId) {
                $sourceId = md5($title . $price); // Fallback ID
            }
            
            // Build full URL if needed
            if ($link && !str_starts_with($link, 'http')) {
                $link = $this->baseUrl . '/' . ltrim($link, '/');
            }
            
            $location = $this->extractLocation($crawler);
            
            // Trouver ou créer la ville - OBLIGATOIRE
            if (!$location) {
                \Illuminate\Support\Facades\Log::warning("No location found for property: {$title}");
                return null;
            }
            
            $city = null;
            try {
                $city = $this->cityService->findOrCreateCity($location);
                if (!$city) {
                    \Illuminate\Support\Facades\Log::warning("Failed to find or create city for: {$location}");
                    return null;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning("Failed to associate city: {$title} - " . $e->getMessage());
                return null;
            }
            
            $propertyData = [
                'source' => $this->source,
                'source_id' => $sourceId,
                'title' => $title,
                'description' => $this->extractText($crawler, '.annonce-texte'),
                'price' => $price,
                'surface' => $this->extractSurfaceFromCrawler($crawler),
                'city_id' => $city->id, // OBLIGATOIRE
                'latitude' => $city->latitude,
                'longitude' => $city->longitude,
                'property_type' => $this->guessPropertyType($title),
                'transaction_type' => $params['transaction_type'] == 2 ? 'rent' : 'sale',
                'url' => $link,
                'images' => $this->extractImagesFromNode($crawler),
                'is_active' => true,
                'scraped_at' => now(),
            ];
            
            \Illuminate\Support\Facades\Log::info("Associated property with city: {$title} -> {$city->name} ({$city->latitude}, {$city->longitude})");
            
            return $propertyData;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error parsing property: ' . $e->getMessage());
            return null;
        }
    }
    
    public function scrapeProperties(array $params = []): array
    {
        $url = $this->buildUrl($params);
        $properties = [];
        $currentPage = $params['page'] ?? 1;
        
        try {
            \Illuminate\Support\Facades\Log::info("Starting scrape for URL: " . $url);
            
            $crawler = $this->fetchPage($url);
            
            if (!$crawler) {
                throw new \Exception("Failed to fetch page");
            }
            
            // Count annonces found
            $annonceCount = $crawler->filter('.annonce-v18')->count();
            \Illuminate\Support\Facades\Log::info("Found {$annonceCount} annonce-v18 elements");
            
            // Extract properties from the page
            $crawler->filter('.annonce-v18')->each(function (Crawler $node, $index) use (&$properties, $url, $params) {
                try {
                    \Illuminate\Support\Facades\Log::info("Processing annonce {$index}");
                    
                    $property = $this->parseProperty($node, $url, $params);
                    if ($property) {
                        $properties[] = $property;
                        \Illuminate\Support\Facades\Log::info("Successfully parsed property: " . $property['title']);
                    } else {
                        \Illuminate\Support\Facades\Log::warning("Failed to parse property at index {$index}");
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Error extracting property: ' . $e->getMessage());
                }
            });
            
            // Save properties to database
            $saveResults = [];
            if (!empty($properties)) {
                $saveResults = $this->saveProperties($properties);
                \Illuminate\Support\Facades\Log::info("Saved to database", $saveResults);
            }
            
            // Check if there are more pages
            $hasNextPage = $this->hasNextPage($crawler);
            
            \Illuminate\Support\Facades\Log::info("Scraping completed. Found " . count($properties) . " properties");
            
            return [
                'properties' => $properties,
                'pagination' => [
                    'current_page' => $currentPage,
                    'has_next_page' => $hasNextPage,
                    'total_found' => count($properties)
                ],
                'database_results' => $saveResults
            ];
            
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Scraping failed: " . $e->getMessage());
            throw new \Exception("Scraping failed: " . $e->getMessage());
        }
    }
    
    protected function buildUrl(array $params): string
    {
        $queryParams = [
            'geo_level' => 'com',
            'marche' => 1,
            'dep' => $params['department'] ?? '19',
            'com' => $params['city'] ?? '',
            'transaction' => $params['transaction_type'] ?? 1,
            'radius' => $params['radius'] ?? 20,
            'prix_f' => $params['min_price'] ?? '',
            'prix_t' => $params['max_price'] ?? '',
            'surface_f' => $params['min_surface'] ?? '',
            'surface_t' => $params['max_surface'] ?? '',
            'terrain_f' => $params['min_terrain'] ?? '',
            'terrain_t' => $params['max_terrain'] ?? '',
        ];
        
        // Add page parameter if needed
        $page = $params['page'] ?? 1;
        if ($page > 1) {
            $queryParams['p'] = $page;
        }
        
        return $this->baseUrl . '/index_catalogue.php?' . http_build_query($queryParams);
    }
    
    protected function extractText(Crawler $node, string $selector): ?string
    {
        try {
            return trim($node->filter($selector)->first()->text());
        } catch (\Exception $e) {
            return null;
        }
    }
    
    protected function extractLocation(Crawler $node): ?string
    {
        // D'abord essayer d'extraire depuis l'URL de la propriété
        try {
            $link = $node->filter('h3 a')->first()->attr('href');
            if ($link) {
                $locationFromUrl = $this->extractLocationFromUrl($link);
                if ($locationFromUrl) {
                    return $locationFromUrl;
                }
            }
        } catch (\Exception $e) {
            // Continue avec les autres méthodes
        }
        
        // Essayer d'extraire la ville du titre
        $title = $this->extractText($node, 'h3 a');
        if ($title) {
            // Nettoyer les balises HTML et normaliser
            $title = str_replace(['<br>', '<br/>', '<br />'], ' ', $title);
            $title = strip_tags($title);
            
            // Chercher le nom de ville après le type de bien
            if (preg_match('/(?:Immeuble|Maison|Appartement|Terrain|Villa)\s+([A-Z][a-z]+(?:\s+[a-z]+)*(?:\s+[A-Z][a-z]+)*)/i', $title, $matches)) {
                return trim($matches[1]);
            }
            
            // Fallback: chercher un nom de ville à la fin du titre
            if (preg_match('/([A-Z][a-z]+(?:\s+[a-z]+)*(?:\s+[A-Z][a-z]+)*)$/i', $title, $matches)) {
                return trim($matches[1]);
            }
        }
        
        // Fallback sur les autres sélecteurs
        $location = $this->extractText($node, '.annonce-commune');
        if (!$location) {
            $location = $this->extractText($node, '.annonce-sub-title');
        }
        
        return $location;
    }
    
    /**
     * Extraire la localisation depuis l'URL de la propriété
     */
    protected function extractLocationFromUrl(string $url): ?string
    {
        // Pattern pour les URLs: /city-name+department+type+transaction+reference.html
        if (preg_match('/\/([^+\/]+)\+(\d{2})\+/', $url, $matches)) {
            $citySlug = $matches[1];
            $department = $matches[2];
            
            // Convertir le slug en nom de ville propre
            $cityName = str_replace('-', ' ', $citySlug);
            $cityName = ucwords($cityName);
            
            // Construire le code postal (département + 00 par défaut)
            $postalCode = $department . '400';
            
            return $cityName . ' - ' . $postalCode;
        }
        
        return null;
    }
    
    protected function hasNextPage(Crawler $crawler): bool
    {
        try {
            // Check for next page link
            return $crawler->filter('.pagination a:contains("Suivant")')->count() > 0 ||
                   $crawler->filter('.pagination .next:not(.disabled)')->count() > 0 ||
                   $crawler->filter('a[rel="next"]')->count() > 0;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    protected function extractSourceId(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        
        // Extract ID from URL pattern
        preg_match('/id=(\d+)/', $url, $matches);
        if (!empty($matches[1])) {
            return $matches[1];
        }
        
        // Try alternative pattern
        preg_match('/(\d+)\.html/', $url, $matches);
        if (!empty($matches[1])) {
            return $matches[1];
        }
        
        // Try another pattern for property URLs
        preg_match('/bien-(\d+)/', $url, $matches);
        if (!empty($matches[1])) {
            return $matches[1];
        }
        
        return md5($url); // Fallback to URL hash
    }
    
    protected function extractSurfaceFromCrawler(Crawler $node): ?float
    {
        $html = $node->html();
        
        // Look for surface area in m²
        preg_match('/(\d+)\s*m²/', $html, $matches);
        if (!empty($matches[1])) {
            return (float) $matches[1];
        }
        
        // Alternative pattern
        preg_match('/(\d+)\s*m2/', $html, $matches);
        if (!empty($matches[1])) {
            return (float) $matches[1];
        }
        
        return null;
    }
    
    protected function extractImagesFromNode(Crawler $node): array
    {
        $images = [];
        
        try {
            $node->filter('img')->each(function (Crawler $img) use (&$images) {
                $src = $img->attr('src');
                if (!$src) {
                    $src = $img->attr('data-src');
                }
                if ($src) {
                    if (!str_starts_with($src, 'http')) {
                        $src = $this->baseUrl . '/' . ltrim($src, '/');
                    }
                    $images[] = $src;
                }
            });
        } catch (\Exception $e) {
            // No images found
        }
        
        return array_unique($images);
    }
    
    protected function guessPropertyType(?string $title): ?string
    {
        if (!$title) {
            return null;
        }
        
        $title = strtolower($title);
        
        if (str_contains($title, 'appartement')) {
            return 'apartment';
        } elseif (str_contains($title, 'maison')) {
            return 'house';
        } elseif (str_contains($title, 'terrain')) {
            return 'land';
        } elseif (str_contains($title, 'garage') || str_contains($title, 'parking')) {
            return 'parking';
        } elseif (str_contains($title, 'local') || str_contains($title, 'commerce')) {
            return 'commercial';
        }
        
        return 'other';
    }
    
    protected function parsePropertyDetailsPage(Crawler $crawler, string $url): array
    {
        return [
            'source_id' => $this->extractSourceId($url),
            'url' => $url,
            'title' => $this->extractText($crawler, 'h1') ?? $this->extractText($crawler, '.titre-annonce'),
            'price' => $this->cleanPrice($this->extractText($crawler, '.prix') ?? $this->extractText($crawler, '.annonce-prix')),
            'description' => $this->extractText($crawler, '.description') ?? $this->extractText($crawler, '.texte-annonce'),
            'images' => $this->extractImages($crawler, 'img'),
            'location' => $this->extractText($crawler, '.localisation') ?? $this->extractText($crawler, '.commune'),
            'surface' => $this->extractSurfaceFromCrawler($crawler),
        ];
    }
}