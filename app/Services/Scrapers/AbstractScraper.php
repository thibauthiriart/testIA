<?php

namespace App\Services\Scrapers;

use App\Models\Property;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

abstract class AbstractScraper
{
    protected string $source;
    protected array $headers = [
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language: fr-FR,fr;q=0.9,en;q=0.8',
        'Accept-Encoding: gzip, deflate, br',
        'Connection: keep-alive',
        'Upgrade-Insecure-Requests: 1',
    ];

    public function __construct()
    {
        // No need for HttpClient constructor anymore
    }

    abstract public function scrapeListingPage(string $url): array;
    
    abstract public function scrapePropertyDetails(string $url): ?array;
    
    abstract protected function parseProperty(Crawler $crawler, string $url): ?array;

    protected function fetchPage(string $url): ?Crawler
    {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_ENCODING, ''); // Handle gzip/deflate
            
            $content = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($content === false || !empty($error)) {
                throw new \Exception("cURL error: " . $error);
            }
            
            if ($httpCode >= 400) {
                throw new \Exception("HTTP error: " . $httpCode);
            }
            
            Log::info("Page fetched successfully", [
                'url' => $url,
                'http_code' => $httpCode,
                'content_length' => strlen($content)
            ]);
            
            return new Crawler($content);
        } catch (\Exception $e) {
            Log::error("Error fetching page: {$url}", [
                'error' => $e->getMessage(),
                'source' => $this->source,
            ]);
            
            return null;
        }
    }

    protected function saveProperties(array $properties): array
    {
        $results = [
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
        ];

        foreach ($properties as $propertyData) {
            try {
                $property = Property::updateOrCreate(
                    [
                        'source' => $this->source,
                        'source_id' => $propertyData['source_id'],
                    ],
                    array_merge($propertyData, [
                        'scraped_at' => now(),
                    ])
                );

                if ($property->wasRecentlyCreated) {
                    $results['created']++;
                } else {
                    $results['updated']++;
                }
            } catch (\Exception $e) {
                $results['failed']++;
                Log::error("Error saving property", [
                    'error' => $e->getMessage(),
                    'source' => $this->source,
                    'source_id' => $propertyData['source_id'] ?? 'unknown',
                ]);
            }
        }

        return $results;
    }

    protected function cleanPrice(?string $price): ?float
    {
        if (empty($price)) {
            return null;
        }

        $price = preg_replace('/[^\d,.]/', '', $price);
        $price = str_replace(',', '.', $price);
        
        return (float) $price;
    }

    protected function cleanSurface(?string $surface): ?float
    {
        if (empty($surface)) {
            return null;
        }

        $surface = preg_replace('/[^\d,.]/', '', $surface);
        $surface = str_replace(',', '.', $surface);
        
        return (float) $surface;
    }

    protected function cleanInteger(?string $value): ?int
    {
        if (empty($value)) {
            return null;
        }

        return (int) preg_replace('/[^\d]/', '', $value);
    }

    protected function extractImages(Crawler $crawler, string $selector): array
    {
        $images = [];
        
        $crawler->filter($selector)->each(function (Crawler $node) use (&$images) {
            $src = $node->attr('src') ?? $node->attr('data-src');
            if ($src) {
                $images[] = $this->normalizeImageUrl($src);
            }
        });

        return array_unique($images);
    }

    protected function normalizeImageUrl(string $url): string
    {
        if (str_starts_with($url, '//')) {
            return 'https:' . $url;
        }
        
        if (!str_starts_with($url, 'http')) {
            return 'https://' . ltrim($url, '/');
        }
        
        return $url;
    }
}