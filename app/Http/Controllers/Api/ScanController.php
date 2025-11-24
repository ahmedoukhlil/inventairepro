<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventaire;
use App\Models\InventaireScan;
use App\Models\Bien;
use App\Services\InventaireService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Controller API pour l'enregistrement des scans depuis la PWA
 * Gère les opérations de scan via l'API
 */
class ScanController extends Controller
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
     * Enregistrer un nouveau scan
     * 
     * @param Request $request
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, Inventaire $inventaire)
    {
        // Valider les données
        $validated = $request->validate([
            'inventaire_localisation_id' => 'required|exists:inventaire_localisations,id',
            'bien_id' => 'required|exists:biens,id',
            'statut_scan' => 'required|in:present,deplace,absent,deteriore',
            'localisation_reelle_id' => 'required|exists:localisations,id',
            'etat_constate' => 'required|in:neuf,bon,moyen,mauvais',
            'commentaire' => 'nullable|string|max:1000',
            'photo' => 'nullable|string', // Base64 image
        ]);

        // Vérifier que l'inventaire est en_cours
        if ($inventaire->statut !== 'en_cours') {
            return response()->json([
                'message' => 'L\'inventaire n\'est pas en cours'
            ], 400);
        }

        // Vérifier que le bien existe et n'est pas soft deleted
        $bien = Bien::find($validated['bien_id']);
        if (!$bien || $bien->trashed()) {
            return response()->json([
                'message' => 'Bien non trouvé ou supprimé'
            ], 404);
        }

        // Vérifier que le bien n'a pas déjà été scanné dans cet inventaire
        $dejaScanne = InventaireScan::where('inventaire_id', $inventaire->id)
            ->where('bien_id', $validated['bien_id'])
            ->exists();

        if ($dejaScanne) {
            return response()->json([
                'message' => 'Ce bien a déjà été scanné dans cet inventaire'
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Traiter la photo si présente
            $photoPath = null;
            if (!empty($validated['photo'])) {
                $photoPath = $this->savePhotoFromBase64($validated['photo'], $bien->code_inventaire);
            }

            // Préparer les données du scan
            $scanData = [
                'inventaire_id' => $inventaire->id,
                'inventaire_localisation_id' => $validated['inventaire_localisation_id'],
                'bien_id' => $validated['bien_id'],
                'statut_scan' => $validated['statut_scan'],
                'localisation_reelle_id' => $validated['localisation_reelle_id'],
                'etat_constate' => $validated['etat_constate'],
                'commentaire' => $validated['commentaire'] ?? null,
                'photo_path' => $photoPath,
                'user_id' => $request->user()->id,
            ];

            // Enregistrer le scan via le service
            $scan = $this->inventaireService->enregistrerScan($scanData);

            DB::commit();

            return response()->json([
                'message' => 'Scan enregistré avec succès',
                'scan' => [
                    'id' => $scan->id,
                    'inventaire_id' => $scan->inventaire_id,
                    'bien_id' => $scan->bien_id,
                    'statut_scan' => $scan->statut_scan,
                    'date_scan' => $scan->date_scan->format('Y-m-d H:i:s'),
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors de l\'enregistrement du scan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Enregistrer plusieurs scans en batch (pour sync offline)
     * 
     * @param Request $request
     * @param Inventaire $inventaire
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeBatch(Request $request, Inventaire $inventaire)
    {
        $validated = $request->validate([
            'scans' => 'required|array|min:1|max:50',
            'scans.*.inventaire_localisation_id' => 'required|exists:inventaire_localisations,id',
            'scans.*.bien_id' => 'required|exists:biens,id',
            'scans.*.statut_scan' => 'required|in:present,deplace,absent,deteriore',
            'scans.*.localisation_reelle_id' => 'required|exists:localisations,id',
            'scans.*.etat_constate' => 'required|in:neuf,bon,moyen,mauvais',
            'scans.*.commentaire' => 'nullable|string|max:1000',
            'scans.*.photo' => 'nullable|string',
        ]);

        // Vérifier que l'inventaire est en_cours
        if ($inventaire->statut !== 'en_cours') {
            return response()->json([
                'message' => 'L\'inventaire n\'est pas en cours'
            ], 400);
        }

        $results = [
            'success' => [],
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($validated['scans'] as $index => $scanData) {
                try {
                    // Vérifier que le bien existe et n'est pas soft deleted
                    $bien = Bien::find($scanData['bien_id']);
                    if (!$bien || $bien->trashed()) {
                        $results['errors'][] = [
                            'index' => $index,
                            'bien_id' => $scanData['bien_id'],
                            'error' => 'Bien non trouvé ou supprimé'
                        ];
                        continue;
                    }

                    // Vérifier doublon
                    $dejaScanne = InventaireScan::where('inventaire_id', $inventaire->id)
                        ->where('bien_id', $scanData['bien_id'])
                        ->exists();

                    if ($dejaScanne) {
                        $results['errors'][] = [
                            'index' => $index,
                            'bien_id' => $scanData['bien_id'],
                            'error' => 'Déjà scanné'
                        ];
                        continue;
                    }

                    // Traiter photo
                    $photoPath = null;
                    if (!empty($scanData['photo'])) {
                        $photoPath = $this->savePhotoFromBase64($scanData['photo'], $bien->code_inventaire);
                    }

                    // Enregistrer via le service
                    $scan = $this->inventaireService->enregistrerScan([
                        'inventaire_id' => $inventaire->id,
                        'inventaire_localisation_id' => $scanData['inventaire_localisation_id'],
                        'bien_id' => $scanData['bien_id'],
                        'statut_scan' => $scanData['statut_scan'],
                        'localisation_reelle_id' => $scanData['localisation_reelle_id'],
                        'etat_constate' => $scanData['etat_constate'],
                        'commentaire' => $scanData['commentaire'] ?? null,
                        'photo_path' => $photoPath,
                        'user_id' => $request->user()->id,
                    ]);

                    $results['success'][] = [
                        'index' => $index,
                        'scan_id' => $scan->id,
                        'bien_id' => $scanData['bien_id']
                    ];

                } catch (\Exception $e) {
                    $results['errors'][] = [
                        'index' => $index,
                        'bien_id' => $scanData['bien_id'] ?? null,
                        'error' => $e->getMessage()
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'message' => 'Batch traité',
                'results' => $results,
                'summary' => [
                    'total' => count($validated['scans']),
                    'success' => count($results['success']),
                    'errors' => count($results['errors'])
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Erreur lors du traitement du batch',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sauvegarder une photo depuis une string Base64
     * 
     * @param string $base64Image
     * @param string $bienCode
     * @return string|null
     */
    private function savePhotoFromBase64($base64Image, $bienCode)
    {
        try {
            // Extraire les données de l'image
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]); // jpg, png, gif, etc.

                // Vérifier le type d'image
                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif'])) {
                    throw new \Exception('Type d\'image non supporté');
                }

                // Décoder
                $image = base64_decode($base64Image);

                if ($image === false) {
                    throw new \Exception('Échec du décodage Base64');
                }

                // Générer un nom de fichier unique
                $filename = 'scan_' . $bienCode . '_' . time() . '_' . Str::random(8) . '.' . $type;
                $path = 'photos/' . date('Y/m') . '/' . $filename;

                // Sauvegarder
                Storage::disk('public')->put($path, $image);

                return $path;

            } else {
                throw new \Exception('Format Base64 invalide');
            }

        } catch (\Exception $e) {
            // Logger l'erreur mais ne pas bloquer le scan
            \Log::error('Erreur sauvegarde photo: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Récupérer l'historique des scans d'un bien
     * 
     * @param Bien $bien
     * @return \Illuminate\Http\JsonResponse
     */
    public function history(Bien $bien)
    {
        // Vérifier que le bien n'est pas soft deleted
        if ($bien->trashed()) {
            return response()->json([
                'message' => 'Ce bien a été supprimé'
            ], 404);
        }

        $scans = InventaireScan::where('bien_id', $bien->id)
            ->with(['inventaire', 'agent', 'localisationReelle'])
            ->orderBy('date_scan', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($scan) {
                return [
                    'id' => $scan->id,
                    'inventaire_annee' => $scan->inventaire->annee ?? null,
                    'date_scan' => $scan->date_scan->format('Y-m-d H:i:s'),
                    'statut_scan' => $scan->statut_scan,
                    'localisation' => $scan->localisationReelle->code ?? null,
                    'agent' => $scan->agent->name ?? null,
                    'commentaire' => $scan->commentaire,
                ];
            });

        return response()->json([
            'scans' => $scans
        ]);
    }
}
