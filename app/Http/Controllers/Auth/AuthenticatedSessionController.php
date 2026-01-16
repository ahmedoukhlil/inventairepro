<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
            'users' => [
                'required',
                'string',
                'max:255',
            ],
            'mdp' => [
                'required',
                'string',
                'max:255',
            ],
            'remember' => 'nullable|boolean',
        ], [
            'users.required' => 'Le nom d\'utilisateur est obligatoire.',
            'users.max' => 'Le nom d\'utilisateur ne peut pas dépasser 255 caractères.',
            'mdp.required' => 'Le mot de passe est obligatoire.',
            'mdp.max' => 'Le mot de passe ne peut pas dépasser 255 caractères.',
        ]);

        // Protection contre les attaques par force brute
        // Laravel gère automatiquement le rate limiting via le middleware throttle
        // Limite : 5 tentatives par minute par IP

        // Récupérer l'utilisateur avec détection automatique de la structure de la table
        // Gère les différences entre environnement local et production
        $user = null;
        
        try {
            // Méthode 1: Utiliser le modèle Eloquent avec whereRaw (plus robuste)
            $user = User::whereRaw('users = ?', [$validated['users']])->first();
            
            if (!$user) {
                // Méthode 2: Essayer avec une requête SQL brute explicite
                $userData = DB::selectOne(
                    'SELECT * FROM users WHERE users.users = ? LIMIT 1',
                    [$validated['users']]
                );
                
                if ($userData) {
                    $userId = $userData->idUser ?? $userData->id ?? null;
                    if ($userId) {
                        $user = User::find($userId);
                    }
                }
            }
        } catch (\Exception $e) {
            // Log de l'erreur pour diagnostic
            \Log::error('Erreur lors de la récupération de l\'utilisateur', [
                'error' => $e->getMessage(),
                'username' => $validated['users'],
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);
            
            // Dernière tentative: utiliser une requête SQL simple sans backticks
            try {
                $userData = DB::selectOne(
                    'SELECT * FROM users WHERE users = ? LIMIT 1',
                    [$validated['users']]
                );
                
                if ($userData) {
                    $userId = $userData->idUser ?? $userData->id ?? null;
                    if ($userId) {
                        $user = User::find($userId);
                    }
                }
            } catch (\Exception $e2) {
                \Log::error('Erreur avec la méthode de fallback', [
                    'error' => $e2->getMessage()
                ]);
                $user = null;
            }
        }

        // Vérifier l'utilisateur et le mot de passe
        if (!$user) {
            throw ValidationException::withMessages([
                'users' => __('Les identifiants fournis sont incorrects.'),
            ]);
        }

        // Vérifier le mot de passe (gérer les mots de passe en clair et hashés)
        $passwordValid = false;
        $storedPassword = $user->mdp;

        // Vérifier si le mot de passe stocké est hashé (commence par $2y$ pour bcrypt)
        if (str_starts_with($storedPassword, '$2y$')) {
            // Mot de passe hashé, utiliser Hash::check
            $passwordValid = Hash::check($validated['mdp'], $storedPassword);
        } else {
            // Mot de passe en clair, comparer directement
            $passwordValid = ($validated['mdp'] === $storedPassword);
            
            // Si la connexion réussit avec un mot de passe en clair, le hasher pour la sécurité
            if ($passwordValid) {
                $user->mdp = Hash::make($validated['mdp']);
                $user->save();
            }
        }

        if (!$passwordValid) {
            \Log::warning('Tentative de connexion échouée', [
                'users' => $validated['users'],
                'ip' => $request->ip(),
            ]);
            throw ValidationException::withMessages([
                'users' => __('Les identifiants fournis sont incorrects.'),
            ]);
        }

        // Connecter l'utilisateur manuellement
        Auth::login($user, $request->boolean('remember'));

        // Régénération de la session pour prévenir les attaques de fixation de session
        $request->session()->regenerate();

        // Initialiser le timestamp de dernière activité
        $request->session()->put('last_activity', now()->timestamp);

        // Log de la connexion réussie (optionnel, pour audit)
        \Log::info('Connexion réussie', [
            'user_id' => $user->idUser,
            'users' => $user->users,
            'role' => $user->role,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Vérifier que la route dashboard existe
        try {
            $dashboardUrl = route('dashboard');
            \Log::info('Redirection vers dashboard', ['url' => $dashboardUrl]);
            return redirect()->intended($dashboardUrl);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la redirection', [
                'error' => $e->getMessage(),
                'route' => 'dashboard'
            ]);
            // Redirection de secours vers la page d'accueil
            return redirect('/');
        }
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

