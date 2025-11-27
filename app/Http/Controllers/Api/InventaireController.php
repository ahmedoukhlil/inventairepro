<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventaire;
use App\Models\InventaireLocalisation;
use App\Services\InventaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller API pour la gestion des inventaires depuis la PWA
 * Gère les opérations d'inventaire via l'API
 */
class InventaireController extends Controller
{
    protected $inventaireService;

    /**
     * Constructeur avec injection de dépendance
     * 
     * @param InventaireService $inventaireService
     */
    public function __construct(InventaireService $inventaireService)
    {
        $this->inventaireService = $inventaireService;
    }

    /**
     * Récupérer l'inventaire actuellement en cours
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function current()
    {
        $inventaire = Inventaire::whereIn('statut', ['en_preparation', 'en_cours'])
            ->with(['creator'])
            ->withCount(['inventaireLocalisations', 'inventaireScans'])
            ->orderBy('annee', 'desc')
            ->first();

        if (!$inventaire) {
            return response()->json([
                'message' => 'Aucun inventaire en cours'
            ], 404);
        }

        // Calculer les statistiques
        $stats = $this->inventaireService->calculerStatistiques($inventaire);

        return response()->json([
            'inventaire' => [
            'id' => $inventaire->id,
            'annee' => $inventaire->annee,
                'date_debut' => $inventaire->date_debut->format('Y-m-d'),
                'date_fin' => $inventaire->date_fin?->format('Y-m-d'),
            'statut' => $inventaire->statut,
                'observation' => $inventaire->observation,
                'created_by' => $inventaire->created_by,
                'creator_name' => $inventaire->creator->name ?? null,
            ],
            'statistiques' => $stats
        ]);
    }

    /**
     * Récupérer les localisations assignées à l'utilisateur pour cet inventaire
     * 
     * @param Request $request
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\JsonResponse
     */
    public function mesLocalisations(Request $request, Inventaire $inventaire)
    {
        $userId = $request->user()->id;

        $localisations = InventaireLocalisation::where('inventaire_id', $inventaire->id)
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhereNull('user_id'); // Localisations non assignées accessibles
            })
            ->with(['localisation'])
            ->get()
            ->map(function ($invLoc) {
                return [
                    'id' => $invLoc->id,
                    'inventaire_id' => $invLoc->inventaire_id,
                    'localisation_id' => $invLoc->localisation_id,
                    'date_debut_scan' => $invLoc->date_debut_scan?->format('Y-m-d H:i:s'),
                    'date_fin_scan' => $invLoc->date_fin_scan?->format('Y-m-d H:i:s'),
                    'statut' => $invLoc->statut,
                    'nombre_biens_attendus' => $invLoc->nombre_biens_attendus,
                    'nombre_biens_scannes' => $invLoc->nombre_biens_scannes,
                    'user_id' => $invLoc->user_id,
                    'localisation' => [
                        'id' => $invLoc->localisation->id,
                        'code' => $invLoc->localisation->code,
                        'designation' => $invLoc->localisation->designation,
                        'batiment' => $invLoc->localisation->batiment,
                        'etage' => $invLoc->localisation->etage,
                        'service_rattache' => $invLoc->localisation->service_rattache,
                        'responsable' => $invLoc->localisation->responsable,
                    ]
                ];
            });

        return response()->json([
            'localisations' => $localisations
        ]);
    }

    /**
     * Démarrer le scan d'une localisation
     * 
     * @param Request $request
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\JsonResponse
     */
    public function demarrerLocalisation(Request $request, Inventaire $inventaire)
    {
        // Valider les données
        $validated = $request->validate([
            'localisation_id' => 'required|exists:localisations,id',
            'user_id' => 'required|exists:users,id',
        ]);

        // Vérifier que l'inventaire est en_cours
        if ($inventaire->statut !== 'en_cours') {
            return response()->json([
                'message' => 'L\'inventaire n\'est pas en cours'
            ], 400);
        }

        // Vérifier que l'utilisateur correspond à celui authentifié
        if ($validated['user_id'] != $request->user()->id) {
            return response()->json([
                'message' => 'Non autorisé'
            ], 403);
        }

        try {
            // Récupérer ou créer l'InventaireLocalisation
            $inventaireLocalisation = InventaireLocalisation::firstOrCreate(
                [
                    'inventaire_id' => $inventaire->id,
                    'localisation_id' => $validated['localisation_id'],
                ],
                [
                    'statut' => 'en_attente',
                    'nombre_biens_attendus' => 0,
                    'nombre_biens_scannes' => 0,
                    'user_id' => $validated['user_id'],
                ]
            );

            // Si déjà en cours ou terminé, retourner
            if (in_array($inventaireLocalisation->statut, ['en_cours', 'termine'])) {
                return response()->json([
                    'message' => 'Localisation déjà en cours ou terminée',
                    'inventaire_localisation' => $this->formatInventaireLocalisation($inventaireLocalisation)
                ]);
        }

            // Démarrer le scan via le service
            $result = $this->inventaireService->demarrerLocalisation(
                $inventaireLocalisation,
                $request->user()
            );

            if (!$result) {
                return response()->json([
                    'message' => 'Impossible de démarrer le scan'
                ], 400);
            }

            // Recharger pour avoir les données à jour
            $inventaireLocalisation->refresh();

        return response()->json([
                'message' => 'Scan de la localisation démarré',
                'inventaire_localisation' => $this->formatInventaireLocalisation($inventaireLocalisation)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors du démarrage du scan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Terminer le scan d'une localisation
     * 
     * @param Request $request
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\JsonResponse
     */
    public function terminerLocalisation(Request $request, Inventaire $inventaire)
    {
        $validated = $request->validate([
            'inventaire_localisation_id' => 'required|exists:inventaire_localisations,id',
        ]);

        $inventaireLocalisation = InventaireLocalisation::findOrFail($validated['inventaire_localisation_id']);

        // Vérifier que c'est bien pour cet inventaire
        if ($inventaireLocalisation->inventaire_id != $inventaire->id) {
            return response()->json([
                'message' => 'Localisation non liée à cet inventaire'
            ], 400);
        }

        // Vérifier que l'utilisateur est celui qui scanne
        if ($inventaireLocalisation->user_id != $request->user()->id) {
            return response()->json([
                'message' => 'Vous n\'êtes pas assigné à cette localisation'
            ], 403);
        }

        try {
            $result = $this->inventaireService->terminerLocalisation($inventaireLocalisation);

            if (!$result) {
                return response()->json([
                    'message' => 'Impossible de terminer la localisation'
                ], 400);
        }

            $inventaireLocalisation->refresh();

        return response()->json([
                'message' => 'Scan de la localisation terminé',
                'inventaire_localisation' => $this->formatInventaireLocalisation($inventaireLocalisation)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erreur lors de la fermeture du scan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtenir les statistiques d'un inventaire
     * 
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\JsonResponse
     */
    public function stats(Inventaire $inventaire)
    {
        $stats = $this->inventaireService->calculerStatistiques($inventaire);

        return response()->json([
            'statistiques' => $stats
        ]);
    }

    /**
     * Formater un InventaireLocalisation pour l'API
     * 
     * @param InventaireLocalisation $invLoc
     * @return array
     */
    private function formatInventaireLocalisation(InventaireLocalisation $invLoc)
    {
        $invLoc->load('localisation');

        return [
            'id' => $invLoc->id,
            'inventaire_id' => $invLoc->inventaire_id,
            'localisation_id' => $invLoc->localisation_id,
            'date_debut_scan' => $invLoc->date_debut_scan?->format('Y-m-d H:i:s'),
            'date_fin_scan' => $invLoc->date_fin_scan?->format('Y-m-d H:i:s'),
            'statut' => $invLoc->statut,
            'nombre_biens_attendus' => $invLoc->nombre_biens_attendus,
            'nombre_biens_scannes' => $invLoc->nombre_biens_scannes,
            'user_id' => $invLoc->user_id,
            'localisation' => [
                'id' => $invLoc->localisation->id,
                'code' => $invLoc->localisation->code,
                'designation' => $invLoc->localisation->designation,
                'batiment' => $invLoc->localisation->batiment,
                'etage' => $invLoc->localisation->etage,
            ]
        ];
    }
}
