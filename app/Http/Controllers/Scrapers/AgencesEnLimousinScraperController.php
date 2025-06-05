<?php

namespace App\Http\Controllers\Scrapers;

use App\Http\Controllers\Controller;
use App\Services\Scrapers\AgencesEnLimousinScraper;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class AgencesEnLimousinScraperController extends Controller
{
    protected AgencesEnLimousinScraper $scraper;

    public function __construct(AgencesEnLimousinScraper $scraper)
    {
        $this->scraper = $scraper;
    }

    public function index()
    {
        return Inertia::render('Properties/ScraperAgencesEnLimousin', [
            'departments' => [
                '19' => 'Corrèze',
                '23' => 'Creuse',
                '87' => 'Haute-Vienne'
            ]
        ]);
    }

    public function scrape(Request $request)
    {
        Log::info('yo');
        $validated = $request->validate([
            'department' => 'required|string',
            'city' => 'nullable|string',
            'transaction_type' => 'required|integer|in:1,2',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'min_surface' => 'nullable|numeric|min:0',
            'max_surface' => 'nullable|numeric|min:0',
            'page' => 'nullable|integer|min:1'
        ]);

        try {
            Log::info('Starting scrape with params:', $validated);

            $result = $this->scraper->scrapeProperties($validated);

            Log::info('Scrape completed successfully', ['properties_count' => count($result['properties'] ?? [])]);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            Log::error('Scraping error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du scraping: ' . $e->getMessage()
            ], 500);
        }
    }
}
