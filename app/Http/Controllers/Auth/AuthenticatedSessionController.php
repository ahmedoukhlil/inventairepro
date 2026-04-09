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
        // Récupérer l'utilisateur avec la méthode helper qui gère automatiquement
        // les différences de structure entre local et production
        $user = User::findByUsername($validated['users']);

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

        // Rediriger vers la première page accessible selon le rôle
        $defaultUrl = $this->getDefaultRedirect($user);
        return redirect()->intended($defaultUrl);
    }

    /**
     * Retourne l'URL de redirection par défaut selon les permissions du user
     */
    private function getDefaultRedirect($user): string
    {
        if ($user->canViewDashboard()) {
            return route('dashboard');
        }

        if ($user->canViewDashboardStock()) {
            return route('stock.dashboard');
        }

        if ($user->canAccessStock()) {
            return route('stock.produits.index');
        }

        if ($user->canManageInventaire()) {
            return route('biens.index');
        }

        // Aucune permission connue : page neutre
        return route('login');
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

