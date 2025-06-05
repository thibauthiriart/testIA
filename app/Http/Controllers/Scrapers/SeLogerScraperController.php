<?php

namespace App\Http\Controllers\Scrapers;

use App\Http\Controllers\Controller;
use App\Services\Scrapers\SeLogerScraper;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SeLogerScraperController extends Controller
{
    protected SeLogerScraper $scraper;

    public function __construct(SeLogerScraper $scraper)
    {
        $this->scraper = $scraper;
    }

    public function scrapeListingPage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Additional SeLoger URL validation
        $url = $request->input('url');
        if (!str_contains($url, 'seloger.com')) {
            return response()->json([
                'success' => false,
                'message' => 'URL must be from seloger.com',
            ], 422);
        }

        try {
            $results = $this->scraper->scrapeListingPage($request->input('url'));

            Log::info('SeLoger listing page scraped', [
                'url' => $request->input('url'),
                'results' => $results,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Scraping completed',
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            Log::error('Error scraping SeLoger listing page', [
                'url' => $request->input('url'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during scraping',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function scrapePropertyDetails(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required|url',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }
        
        // Additional SeLoger URL validation
        $url = $request->input('url');
        if (!str_contains($url, 'seloger.com')) {
            return response()->json([
                'success' => false,
                'message' => 'URL must be from seloger.com',
            ], 422);
        }

        try {
            $property = $this->scraper->scrapePropertyDetails($request->input('url'));

            if (!$property) {
                return response()->json([
                    'success' => false,
                    'message' => 'Property not found or could not be scraped',
                ], 404);
            }

            Log::info('SeLoger property details scraped', [
                'url' => $request->input('url'),
                'property_id' => $property['source_id'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Property details scraped',
                'property' => $property,
            ]);
        } catch (\Exception $e) {
            Log::error('Error scraping SeLoger property details', [
                'url' => $request->input('url'),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during scraping',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function scrapeMultiplePages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'base_url' => 'required|url',
            'pages' => 'required|integer|min:1|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $baseUrl = $request->input('base_url');
        $pages = $request->input('pages');
        $totalResults = [
            'created' => 0,
            'updated' => 0,
            'failed' => 0,
        ];

        try {
            for ($page = 1; $page <= $pages; $page++) {
                $separator = str_contains($baseUrl, '?') ? '&' : '?';
                $url = $baseUrl . $separator . 'LISTING-LISTpg=' . $page;
                
                $results = $this->scraper->scrapeListingPage($url);
                
                $totalResults['created'] += $results['created'] ?? 0;
                $totalResults['updated'] += $results['updated'] ?? 0;
                $totalResults['failed'] += $results['failed'] ?? 0;

                sleep(rand(2, 5));
            }

            Log::info('SeLoger multiple pages scraped', [
                'base_url' => $baseUrl,
                'pages' => $pages,
                'results' => $totalResults,
            ]);

            return response()->json([
                'success' => true,
                'message' => "Scraped {$pages} pages",
                'results' => $totalResults,
            ]);
        } catch (\Exception $e) {
            Log::error('Error scraping SeLoger multiple pages', [
                'base_url' => $baseUrl,
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error during scraping',
                'error' => $e->getMessage(),
                'partial_results' => $totalResults,
            ], 500);
        }
    }

}
