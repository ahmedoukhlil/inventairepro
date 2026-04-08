<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CanAccessStock
{
    /**
     * Vérifie que l'utilisateur peut accéder au module Stock.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        $user = Auth::user();

        if (!$user->canAccessStock()) {
            return redirect()->route('dashboard')
                ->with('error', 'Accès non autorisé au module stock.');
        }

        return $next($request);
    }
}

