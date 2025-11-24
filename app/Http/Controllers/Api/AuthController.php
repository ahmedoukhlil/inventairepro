<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Controller d'authentification pour l'API PWA
 * Gère l'authentification via Laravel Sanctum
 */
class AuthController extends Controller
{
    /**
     * Authentifier un utilisateur et retourner un token Sanctum
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // Valider les données
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        // Récupérer l'utilisateur
        $user = User::where('email', $request->email)->first();

        // Vérifier l'utilisateur et le mot de passe
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants fournis sont incorrects.'],
            ]);
        }

        // Vérifier que l'utilisateur est actif
        if (!$user->actif) {
            return response()->json([
                'message' => 'Votre compte est désactivé. Contactez un administrateur.'
            ], 403);
        }

        // Vérifier que l'utilisateur a le rôle approprié (agent ou admin)
        if (!in_array($user->role, ['agent', 'admin'])) {
            return response()->json([
                'message' => 'Accès non autorisé pour ce type de compte.'
            ], 403);
        }

        // Révoquer les tokens existants (optionnel, pour single device login)
        // Décommenter la ligne suivante pour forcer une seule session active
        // $user->tokens()->delete();

        // Créer un nouveau token Sanctum
        $token = $user->createToken('pwa-scanner')->plainTextToken;

        // Retourner les données
        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'telephone' => $user->telephone,
                'service' => $user->service,
            ]
        ]);
    }

    /**
     * Déconnecter l'utilisateur (révoquer le token actuel)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // Vérifier que l'utilisateur est authentifié
        if (!$request->user()) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

        // Révoquer le token actuel
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie'
        ]);
    }

    /**
     * Obtenir les informations de l'utilisateur authentifié
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'telephone' => $user->telephone,
                'service' => $user->service,
                'actif' => $user->actif,
            ]
        ]);
    }

    /**
     * Rafraîchir le token (optionnel)
     * Crée un nouveau token et révoque l'ancien
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'message' => 'Non authentifié'
            ], 401);
        }

        // Révoquer l'ancien token
        $request->user()->currentAccessToken()->delete();

        // Créer un nouveau token
        $token = $user->createToken('pwa-scanner')->plainTextToken;

        return response()->json([
            'token' => $token,
            'message' => 'Token rafraîchi avec succès'
        ]);
    }
}
