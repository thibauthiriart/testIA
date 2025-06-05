<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class ScraperController extends Controller
{
    public function index()
    {
        return Inertia::render('Properties/Scraper');
    }
}
