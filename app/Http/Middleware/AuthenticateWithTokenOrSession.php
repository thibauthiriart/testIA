<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\TokenRepository;
use Laravel\Passport\Token;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateWithTokenOrSession
{
    public function handle(Request $request, Closure $next): Response
    {
        // D'abord essayer l'authentification par session (comportement normal)
        if (Auth::check()) {
            return $next($request);
        }

        // Si pas de session, essayer l'authentification par token
        $token = $this->getTokenFromRequest($request);
        
        if ($token) {
            try {
                // Trouver le token dans la base
                $accessToken = app(TokenRepository::class)->find($token);
                
                if ($accessToken && !$accessToken->revoked && $accessToken->expires_at->isFuture()) {
                    // Token valide, authentifier l'utilisateur
                    Auth::loginUsingId($accessToken->user_id);
                    return $next($request);
                }
            } catch (\Exception $e) {
                // Token invalide, continuer
            }
        }

        // Aucune authentification valide
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('login'));
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        // Chercher le token dans les headers Authorization
        $header = $request->header('Authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        // Chercher dans le header X-Auth-Token
        return $request->header('X-Auth-Token');
    }
}