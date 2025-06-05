<?php

namespace App\Services\Scrapers;

use Illuminate\Support\Facades\Log;
use Symfony\Component\DomCrawler\Crawler;

class SeLogerScraper extends AbstractScraper
{
    protected string $source = 'seloger';
    protected string $baseUrl = 'https://www.seloger.com';

    public function scrapeListingPage(string $url): array
    {
        $crawler = $this->fetchPage($url);

        if (!$crawler) {
            return ['created' => 0, 'updated' => 0, 'failed' => 0];
        }

        $properties = [];
        $html = $crawler->html();

        Log::info('SeLoger page fetched', [
            'url' => $url,
            'html_length' => strlen($html)
        ]);

        // Method 1: Extract from __NEXT_DATA__ script (most reliable for modern SeLoger)
        try {
            if (preg_match('/<script id="__NEXT_DATA__"[^>]*>(.*?)<\/script>/s', $html, $matches)) {
                $nextData = json_decode($matches[1], true);

                if ($nextData) {
                    Log::info('Found __NEXT_DATA__', ['keys' => array_keys($nextData)]);

                    // Try different possible paths for SeLoger data structure
                    $possiblePaths = [
                        'props.pageProps.searchData.cards.list',
                        'props.pageProps.listings.cards.list',
                        'props.pageProps.initialData.listings.cards.list',
                        'props.pageProps.data.cards.list',
                        'props.pageProps.searchData.results',
                        'props.pageProps.listings',
                        'props.pageProps.cards'
                    ];

                    foreach ($possiblePaths as $path) {
                        $data = $this->getNestedValue($nextData, $path);
                        if ($data && is_array($data)) {
                            Log::info('Found data at path: ' . $path, ['count' => count($data)]);

                            foreach ($data as $item) {
                                if (is_array($item)) {
                                    // Skip ads and only process real estate listings
                                    if (isset($item['cardType']) && $item['cardType'] !== 'classified') {
                                        continue;
                                    }

                                    $property = $this->parsePropertyFromJson($item);
                                    if ($property) {
                                        $properties[] = $property;
                                    }
                                }
                            }

                            if (!empty($properties)) {
                                break; // Found valid data, stop searching
                            }
                        }
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error parsing __NEXT_DATA__: ' . $e->getMessage());
        }

        // Method 2: Look for embedded JSON in script tags
        if (empty($properties)) {
            try {
                // Look for various patterns where SeLoger might embed data
                $patterns = [
                    '/window\["__SELOGER_DATA__"\]\s*=\s*({.+?});/s',
                    '/window\.__SELOGER_DATA__\s*=\s*({.+?});/s',
                    '/window\["initialData"\]\s*=\s*({.+?});/s',
                    '/window\.initialData\s*=\s*({.+?});/s',
                    '/"listings":\s*({.+?})/s',
                    '/"cards":\s*\[(.+?)\]/s'
                ];

                foreach ($patterns as $pattern) {
                    if (preg_match($pattern, $html, $matches)) {
                        Log::info('Found data with pattern', ['pattern' => $pattern]);

                        $jsonString = $matches[1];
                        $data = json_decode($jsonString, true);

                        if ($data && is_array($data)) {
                            // Process the found data
                            $this->processJsonData($data, $properties);

                            if (!empty($properties)) {
                                break;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error parsing embedded JSON: ' . $e->getMessage());
            }
        }

        // Method 3: Parse HTML structure directly (fallback)
        if (empty($properties)) {
            try {
                // SeLoger uses various CSS classes for listing cards
                $selectors = [
                    '.listing-card',
                    '.search-list-item',
                    '.card-listing',
                    '[data-listing-id]',
                    '.listing-item',
                    '.property-card'
                ];

                foreach ($selectors as $selector) {
                    $cards = $crawler->filter($selector);
                    if ($cards->count() > 0) {
                        Log::info('Found cards with selector: ' . $selector, ['count' => $cards->count()]);

                        $cards->each(function (Crawler $card) use (&$properties) {
                            $property = $this->parsePropertyFromHtml($card);
                            if ($property) {
                                $properties[] = $property;
                            }
                        });

                        if (!empty($properties)) {
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Error parsing HTML structure: ' . $e->getMessage());
            }
        }

        Log::info('SeLoger scraping completed', [
            'url' => $url,
            'properties_found' => count($properties),
            'html_preview' => substr($html, 0, 500)
        ]);

        return $this->saveProperties($properties);
    }

    public function scrapePropertyDetails(string $url): ?array
    {
        $crawler = $this->fetchPage($url);

        if (!$crawler) {
            return null;
        }

        return $this->parseDetailedProperty($crawler, $url);
    }

    protected function parseProperty(Crawler $crawler, string $url): ?array
    {
        // This method is not used anymore as SeLoger uses JSON data
        // We keep it to satisfy the abstract class requirement
        return null;
    }

    protected function parsePropertyFromJson(array $card): ?array
    {
        try {
            // Extract URL and source ID
            $url = isset($card['link']) ? $this->baseUrl . $card['link'] : '';
            $sourceId = isset($card['id']) ? (string)$card['id'] : null;

            if (!$sourceId || !$url) {
                return null;
            }

            // Extract basic information
            $title = $card['title'] ?? '';
            $price = $card['pricing']['price'] ?? $card['pricing']['raw_price'] ?? 0;

            // Extract location information
            $city = $card['city_label'] ?? '';
            $postalCode = $card['zip_code'] ?? '';
            $department = substr($postalCode, 0, 2);

            // Extract property details from tags
            $surface = null;
            $rooms = null;
            $bedrooms = null;

            if (isset($card['tags']) && is_array($card['tags'])) {
                foreach ($card['tags'] as $tag) {
                    if (preg_match('/(\d+)\s*m²/i', $tag, $matches)) {
                        $surface = $matches[1];
                    } elseif (preg_match('/(\d+)\s*pièces?/i', $tag, $matches)) {
                        $rooms = $matches[1];
                    } elseif (preg_match('/(\d+)\s*chambres?/i', $tag, $matches)) {
                        $bedrooms = $matches[1];
                    }
                }
            }

            // Extract images
            $images = [];
            if (isset($card['photos']) && is_array($card['photos'])) {
                foreach ($card['photos'] as $photo) {
                    if (is_string($photo)) {
                        $images[] = $this->normalizeImageUrl($photo);
                    } elseif (is_array($photo) && isset($photo['url'])) {
                        $images[] = $this->normalizeImageUrl($photo['url']);
                    }
                }
            }

            // Extract coordinates if available
            $latitude = $card['latitude'] ?? null;
            $longitude = $card['longitude'] ?? null;

            return [
                'source' => $this->source,
                'source_id' => $sourceId,
                'title' => $title,
                'description' => $card['description'] ?? null,
                'price' => $this->cleanPrice($price),
                'surface' => $this->cleanSurface($surface),
                'rooms' => $this->cleanInteger($rooms),
                'bedrooms' => $this->cleanInteger($bedrooms),
                'city' => $city,
                'postal_code' => $postalCode,
                'department' => $department,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'property_type' => $this->extractPropertyType($title),
                'transaction_type' => $this->extractTransactionType($url),
                'url' => $url,
                'images' => $images,
                'additional_info' => $card['tags'] ?? [],
                'is_active' => true,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function parseDetailedProperty(Crawler $crawler, string $url): ?array
    {
        try {
            $sourceId = $this->extractSourceId($url);

            if (!$sourceId) {
                return null;
            }

            // Try to extract from __NEXT_DATA__ script tag first
            $nextDataScript = $crawler->filter('script#__NEXT_DATA__')->text('');
            if ($nextDataScript) {
                $nextData = json_decode($nextDataScript, true);
                if (isset($nextData['props']['pageProps']['listingData'])) {
                    return $this->parseDetailedPropertyFromJson($nextData['props']['pageProps']['listingData'], $url, $sourceId);
                }
            }

            // Fallback to traditional HTML parsing if JSON method fails
            $title = $crawler->filter('h1')->text('');
            $price = $crawler->filter('[class*="Price__Label"]')->text('');
            $description = $crawler->filter('[data-test="sl.description"]')->text('');

            $details = [];
            $crawler->filter('[data-test="sl.summary-item"]')->each(function (Crawler $node) use (&$details) {
                $label = $node->filter('[data-test="sl.summary-item-label"]')->text('');
                $value = $node->filter('[data-test="sl.summary-item-value"]')->text('');
                $details[$label] = $value;
            });

            $surface = $details['Surface'] ?? '';
            $rooms = $details['Pièces'] ?? '';
            $bedrooms = $details['Chambres'] ?? '';

            $location = $crawler->filter('[class*="Summary__Address"]')->text('');
            $cityParts = explode(' ', $location);
            $postalCode = '';
            $city = $location;

            foreach ($cityParts as $part) {
                if (preg_match('/^\d{5}$/', $part)) {
                    $postalCode = $part;
                    $city = trim(str_replace($postalCode, '', $location));
                    break;
                }
            }

            $department = substr($postalCode, 0, 2);

            $images = $this->extractImages($crawler, '[data-test="sl.gallery-photo"] img');

            $coordinates = $this->extractCoordinates($crawler);

            return [
                'source' => $this->source,
                'source_id' => $sourceId,
                'title' => $title,
                'description' => $description,
                'price' => $this->cleanPrice($price),
                'surface' => $this->cleanSurface($surface),
                'rooms' => $this->cleanInteger($rooms),
                'bedrooms' => $this->cleanInteger($bedrooms),
                'city' => $city,
                'postal_code' => $postalCode,
                'department' => $department,
                'latitude' => $coordinates['latitude'] ?? null,
                'longitude' => $coordinates['longitude'] ?? null,
                'property_type' => $this->extractPropertyType($title),
                'transaction_type' => $this->extractTransactionType($url),
                'url' => $url,
                'images' => $images,
                'additional_info' => $details,
                'is_active' => true,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function extractSourceId(string $url): ?string
    {
        if (preg_match('/annonces\/(\d+)\.htm/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    protected function extractPropertyType(string $title): string
    {
        $title = mb_strtolower($title);

        $types = [
            'appartement' => 'apartment',
            'maison' => 'house',
            'villa' => 'villa',
            'studio' => 'studio',
            'loft' => 'loft',
            'château' => 'castle',
            'terrain' => 'land',
            'parking' => 'parking',
            'garage' => 'garage',
        ];

        foreach ($types as $french => $english) {
            if (str_contains($title, $french)) {
                return $english;
            }
        }

        return 'other';
    }

    protected function extractTransactionType(string $url): string
    {
        if (str_contains($url, '/locations/')) {
            return 'rent';
        }

        return 'sale';
    }

    protected function parseDetailedPropertyFromJson(array $listingData, string $url, string $sourceId): ?array
    {
        try {
            $listing = $listingData['listing'] ?? [];
            $agency = $listingData['agency'] ?? [];

            // Extract basic information
            $title = $listing['title'] ?? '';
            $description = $listing['description'] ?? '';
            $price = $listing['price'] ?? 0;

            // Extract location
            $address = $listing['address'] ?? [];
            $city = $address['city'] ?? '';
            $postalCode = $address['postal_code'] ?? '';
            $department = substr($postalCode, 0, 2);

            // Extract property details
            $surface = $listing['surface'] ?? null;
            $rooms = $listing['rooms'] ?? null;
            $bedrooms = $listing['bedrooms'] ?? null;

            // Extract coordinates
            $coordinates = $listing['coordinates'] ?? [];
            $latitude = $coordinates['latitude'] ?? null;
            $longitude = $coordinates['longitude'] ?? null;

            // Extract images
            $images = [];
            if (isset($listing['photos']) && is_array($listing['photos'])) {
                foreach ($listing['photos'] as $photo) {
                    if (is_string($photo)) {
                        $images[] = $this->normalizeImageUrl($photo);
                    } elseif (is_array($photo) && isset($photo['url'])) {
                        $images[] = $this->normalizeImageUrl($photo['url']);
                    }
                }
            }

            // Extract additional details
            $additionalInfo = [];
            if (isset($listing['characteristics']) && is_array($listing['characteristics'])) {
                $additionalInfo = $listing['characteristics'];
            }

            return [
                'source' => $this->source,
                'source_id' => $sourceId,
                'title' => $title,
                'description' => $description,
                'price' => $this->cleanPrice($price),
                'surface' => $this->cleanSurface($surface),
                'rooms' => $this->cleanInteger($rooms),
                'bedrooms' => $this->cleanInteger($bedrooms),
                'city' => $city,
                'postal_code' => $postalCode,
                'department' => $department,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'property_type' => $this->extractPropertyType($title),
                'transaction_type' => $this->extractTransactionType($url),
                'url' => $url,
                'images' => $images,
                'additional_info' => $additionalInfo,
                'is_active' => true,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function extractCoordinates(Crawler $crawler): array
    {
        $coordinates = [];

        try {
            $mapData = $crawler->filter('[data-map]')->attr('data-map');
            if ($mapData) {
                $data = json_decode($mapData, true);
                $coordinates['latitude'] = $data['latitude'] ?? null;
                $coordinates['longitude'] = $data['longitude'] ?? null;
            }
        } catch (\Exception $e) {
            // Silent fail
        }

        return $coordinates;
    }

    protected function getNestedValue(array $array, string $path): mixed
    {
        $keys = explode('.', $path);
        $current = $array;

        foreach ($keys as $key) {
            if (!is_array($current) || !isset($current[$key])) {
                return null;
            }
            $current = $current[$key];
        }

        return $current;
    }

    protected function parsePropertyFromHtml(Crawler $card): ?array
    {
        try {
            // Fallback HTML parsing if JSON methods fail
            $titleElement = $card->filter('h3, .listing-title, [data-test="listing-title"]');
            $title = $titleElement->count() > 0 ? $titleElement->text('') : '';

            $priceElement = $card->filter('.price, .listing-price, [data-test="listing-price"]');
            $price = $priceElement->count() > 0 ? $priceElement->text('') : '';

            $locationElement = $card->filter('.location, .listing-location, [data-test="listing-location"]');
            $location = $locationElement->count() > 0 ? $locationElement->text('') : '';

            $linkElement = $card->filter('a');
            $url = $linkElement->count() > 0 ? $linkElement->attr('href') : '';

            if ($url && !str_starts_with($url, 'http')) {
                $url = $this->baseUrl . $url;
            }

            $sourceId = $this->extractSourceId($url);

            if (!$sourceId || !$title) {
                return null;
            }

            // Extract city and postal code from location
            $cityParts = explode(' ', $location);
            $postalCode = '';
            $city = $location;

            foreach ($cityParts as $part) {
                if (preg_match('/^\d{5}$/', $part)) {
                    $postalCode = $part;
                    $city = trim(str_replace($postalCode, '', $location));
                    break;
                }
            }

            $department = substr($postalCode, 0, 2);

            return [
                'source' => $this->source,
                'source_id' => $sourceId,
                'title' => $title,
                'description' => null,
                'price' => $this->cleanPrice($price),
                'surface' => null,
                'rooms' => null,
                'bedrooms' => null,
                'city' => $city,
                'postal_code' => $postalCode,
                'department' => $department,
                'latitude' => null,
                'longitude' => null,
                'property_type' => $this->extractPropertyType($title),
                'transaction_type' => $this->extractTransactionType($url),
                'url' => $url,
                'images' => [],
                'additional_info' => [],
                'is_active' => true,
            ];
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function processJsonData(array $data, array &$properties): void
    {
        // Recursively process JSON data to find property listings
        if (isset($data['list']) && is_array($data['list'])) {
            foreach ($data['list'] as $item) {
                if (is_array($item)) {
                    $property = $this->parsePropertyFromJson($item);
                    if ($property) {
                        $properties[] = $property;
                    }
                }
            }
        } elseif (isset($data['cards']) && is_array($data['cards'])) {
            foreach ($data['cards'] as $item) {
                if (is_array($item)) {
                    $property = $this->parsePropertyFromJson($item);
                    if ($property) {
                        $properties[] = $property;
                    }
                }
            }
        } elseif (isset($data['results']) && is_array($data['results'])) {
            foreach ($data['results'] as $item) {
                if (is_array($item)) {
                    $property = $this->parsePropertyFromJson($item);
                    if ($property) {
                        $properties[] = $property;
                    }
                }
            }
        }

        // If it's a direct array of properties
        if (empty($properties) && is_array($data)) {
            foreach ($data as $item) {
                if (is_array($item) && isset($item['id']) && isset($item['title'])) {
                    $property = $this->parsePropertyFromJson($item);
                    if ($property) {
                        $properties[] = $property;
                    }
                }
            }
        }
    }

}
