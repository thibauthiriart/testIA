<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Accès non autorisé.'
                ], 403);
            }
            
            return Inertia::render('Errors/403')->toResponse($request);
        }

        return $next($request);
    }
}