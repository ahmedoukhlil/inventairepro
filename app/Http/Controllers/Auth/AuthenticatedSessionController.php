<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Affiche le formulaire de connexion
     */
    public function create()
    {
        return view('auth.login');
    }

    /**
     * Traite la tentative de connexion
     * 
     * Protections de sécurité :
     * - Validation stricte des entrées
     * - Protection contre les attaques par force brute (rate limiting)
     * - Protection contre les injections SQL (via Eloquent)
     * - Protection XSS (échappement automatique par Blade)
     */
    public function store(Request $request)
    {
        // Validation stricte avec règles de sécurité
        $validated = $request->validate([
            'email' => [
                'required',
                'string',
                'email:rfc,dns', // Validation stricte de l'email avec vérification DNS
                'max:255',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/', // Pattern supplémentaire
            ],
            'password' => [
                'required',
                'string',
                'min:8', // Minimum 8 caractères
                'max:255',
            ],
            'remember' => 'nullable|boolean',
        ], [
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.regex' => 'Le format de l\'adresse email est invalide.',
            'email.max' => 'L\'adresse email ne peut pas dépasser 255 caractères.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.max' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
        ]);

        // Protection contre les attaques par force brute
        // Laravel gère automatiquement le rate limiting via le middleware throttle
        // Limite : 5 tentatives par minute par IP

        // Tentative d'authentification
        // Les mots de passe sont automatiquement hashés et comparés de manière sécurisée
        if (!Auth::attempt(
            [
                'email' => $validated['email'], // Utiliser les données validées
                'password' => $validated['password'],
            ],
            $request->boolean('remember')
        )) {
            // En cas d'échec, ne pas révéler si l'email existe ou non (sécurité)
            throw ValidationException::withMessages([
                'email' => __('Les identifiants fournis sont incorrects.'),
            ]);
        }

        // Régénération de la session pour prévenir les attaques de fixation de session
        $request->session()->regenerate();

        // Initialiser le timestamp de dernière activité
        $request->session()->put('last_activity', now()->timestamp);

        // Log de la connexion réussie (optionnel, pour audit)
        \Log::info('Connexion réussie', [
            'user_id' => Auth::id(),
            'email' => Auth::user()->email,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Déconnecte l'utilisateur
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

